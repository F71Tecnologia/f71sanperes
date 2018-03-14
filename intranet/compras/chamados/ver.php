<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../../conn.php");
include("../../classes/global.php");
include("../../classes/ComprasChamados.php");
include("../../wfunction.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$projeto1 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto1' name='projeto' class='form-control validate[required,custom[select]]'");
$global = new GlobalClass();

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "35", "area" => "Gestão de Compras e Contratos", "ativo" => "Detalhe do Chamado", "id_form" => "form-pedido");
$breadcrumb_pages = array("Chamados a Prestadores" => "../chamados");

$id_chamado = $_REQUEST['id_chamado'];
$objChamado = new ComprasChamados();

if(isset($_REQUEST['responder']) || isset($_REQUEST['cancelaSol']) || isset($_REQUEST['encerraSol']) || isset($_REQUEST['voltarAtiva']) || isset($_REQUEST['toPendente'])){
    // 1=aberto, 3=encerrados, 4=cancelados, 5=pendente
    if(isset($_REQUEST['mensagem']) && !empty($_REQUEST['mensagem'])){
        $dados['msg'] = utf8_encode($_REQUEST['mensagem']);
        $dados['usuario'] = utf8_encode($usuario['nome1']);
    }
    
    if(isset($_REQUEST['responder']) && !empty($_REQUEST['responder'])){
        $dados['status'] = 1;
    }elseif(isset($_REQUEST['cancelaSol']) && !empty($_REQUEST['cancelaSol'])){
        $dados['status'] = 4;
    }elseif(isset($_REQUEST['encerraSol']) && !empty($_REQUEST['encerraSol'])){
        $dados['status'] = 3;
    }elseif(isset($_REQUEST['voltarAtiva']) && !empty($_REQUEST['voltarAtiva'])){
        $dados['status'] = 1;
    }elseif(isset($_REQUEST['toPendente']) && !empty($_REQUEST['toPendente'])){
        $dados['status'] = 5;
    }
    
    $objChamado->atualizaChamado($id_chamado,$dados);
    header("Location: index.php");
    exit;
}

$chamado = $objChamado->getChamado($id_chamado);

//TIPOS DE ARQUIVOS ANEXO
$imagesFiles = array('jpg','png','gif');
$videosFiles = array('mp4','3gp');
$cont = 0;
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="../../favicon.png">

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" type="text/css">

    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <form action="ver.php" method="post" name="form1" id="form1" class="form-horizontal top-margin1">
                <input type="hidden" name="id_chamado" id="id_chamado" value="<?php echo $id_chamado ?>" />
                <div class="row">
                    <div class="col-lg-12">
                        <div class="page-header box-compras-header">
                            <h2><span class="glyphicon glyphicon-shopping-cart"></span> - Gestão de Compras e Contratos <small>- Detalhe do Chamado</small></h2>
                        </div>
                        
                        <div class="panel panel-default">
                            <div class="panel-heading text-bold">Detalhe do Chamado</div>
                            <div class='panel-body'>
                        
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Aberto Por</label>
                                    <div class="col-lg-10">
                                        <?php echo utf8_decode($chamado['chamado']['usuario_criou'])?>
                                    </div>
                                </div>
                            </div>
                                
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Projeto</label>
                                    <div class="col-lg-10">
                                        <?php echo utf8_decode($chamado['chamado']['nome_projeto'])?>
                                    </div>
                                </div>
                            </div>
                                
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Prestador</label>
                                    <div class="col-lg-10">
                                        <?php echo utf8_decode($chamado['chamado']['nome_prestador'])?>
                                    </div>
                                </div>
                            </div>
                                
                            <div class="clearfix"></div>
                            <?php if(count($chamado['anexos'])>0){?>
                            <p></p>
                            <h3>Anexo</h3>
                            <hr>

                            <div class="gallery">
                                <?php foreach ($chamado['anexos'] as $anexo) { ?>
                                
                                <?php if(in_array($anexo['tipo_arquivo'],$imagesFiles)) { ?>
                                <a href="http://suporte.institutolagosrio.com.br/resources/upload/<?php echo $anexo['id_chamado'] . "/" . $anexo['url'] ?>" class="img-thumbnail col-lg-2" target="_blanck"><img src="http://suporte.institutolagosrio.com.br/resources/upload/<?php echo $anexo['id_chamado'] . "/" . $anexo['url'] ?>" alt="" class="img-responsive"></a>
                                <?php } ?>
                                
                                <?php if(in_array($anexo['tipo_arquivo'],$videosFiles)) { 
                                $videos[] = '<video width="400" controls>
                                           <source src="http://suporte.institutolagosrio.com.br/resources/upload/'. $anexo['id_chamado'] . "/" . $anexo['url'].'" type="video/mp4">
                                           </video>';
                                } ?>
                            <?php } ?>
                            </div>
                            
                            <?php if(count($videos) > 0){ foreach ($videos as $video) {
                                echo "<div class='form-group' style='margin-left:10px'> \r\n".$video."</div>";
                            }}?>

                            <div class="clearfix"></div>
                            <?php } ?>
                            
                            <p></p>
                            <h3>Mensagens</h3>
                            <hr>

                            <div class="timeline centered" style="padding-bottom: 0;">
                                <?php if($chamado['chamado']['tipo'] == 1){ ?>
                                <div class="tl-header now btn btn-success">Abertura do Chamado <br> <?php echo $chamado['chamado']['aberto_emBR'] ?></div>
                                <?php }else{ ?>
                                <div class="tl-header now btn btn-success">Abertura do Alerta <br> <?php echo $chamado['chamado']['alertado_emBR'] ?></div>
                                <?php } ?>
                                
                                <?php foreach ($chamado['mensagens'] as $k => $msg) { ?>
                                    <div class="tl-entry<?php echo ($cont++ % 2 == 1) ? '' : ' left' ?>">
                                        <div class="tl-time"><?php echo $msg['dataBR'] ?></div>
                                        <?php if ($msg['tipo'] == 1) { ?><div class="tl-icon bg-info"><i class="fa fa-check"></i></div><?php } ?>
                                        <?php if ($msg['tipo'] == 2) { ?><div class="tl-icon bg-success"><i class="fa fa-calendar"></i></div><?php } ?>
                                        <div class="panel tl-body">
                                            <h4 class="text-warning"><?php echo utf8_decode($msg['responsavel']) ?></h4> <?php echo nl2br(utf8_decode($msg['mensagem'])) ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($chamado['chamado']['status'] >= 3 && $chamado['chamado']['status'] != 5) { ?>
                                <div class="tl-header now btn <?php echo ($chamado['chamado']['status'] == 3) ? 'btn-success' : 'btn-danger' ?>" style="margin-bottom: 0px;"><?php echo ($chamado['chamado']['status'] == 3) ? 'Encerrado' : 'Cancelado' ?> <br> <?php echo $chamado['chamado']['fechado_emBR'] ?> </div>
                                <?php } ?>
                            </div>

                            <?php if ($chamado['chamado']['tipo'] == 1 && ($chamado['chamado']['status'] < 3 || $chamado['chamado']['status'] == 5)) { ?>
                                <input type="hidden" name="id_chamado" id="id_chamado" value="<?php echo $chamado['chamado']['id_chamado'] ?>" >
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Mensagem</label>
                                    <div class="col-lg-10">
                                        <textarea name="mensagem" id="msg" rows="10" class='form-control'></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-2 control-label"></label>
                                    <div class="col-lg-10">
                                        <button type="submit" name="responder" value="Resp" class="btn btn-success">Responder</button>
                                        <p></p>
                                        <p></p>

                                        <button type="submit" name="cancelaSol" value="Cancelar" class="btn btn-danger">Cancelar Solicitação</button>
                                        <p></p>
                                        <p></p> 

                                        <button type="submit" name="encerraSol" value="Ecnerrar" class="btn btn-info">Encerrar Solicitação</button>
                                        <p></p>
                                        <p></p>
                                        
                                        
                                        <?php if($chamado['chamado']['status'] == 5){?>
                                            <button type="submit" name="voltarAtiva" value="voltarAtiva" class="btn btn-success">Voltar a Ativa</button>
                                        <?php }else{ ?>
                                            <button type="submit" name="toPendente" value="toPendente" class="btn btn-warning">Passar para Pendente</button>
                                        <?php } ?>    
                                    </div>
                                </div>

                                <div class="clearfix"></div>
                            <?php } ?>
                            </div>
                            <div class="panel-footer text-right">
                                <a href="javascript:history.go(-1);" class="btn btn-default">Voltar</a>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </form>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        
    </body>
</html>