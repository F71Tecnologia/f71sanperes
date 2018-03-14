<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/SuporteClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$botoes = new BotoesClass();
$classDefaults = $botoes->classModulos;
$modulos = $botoes->getModulos();
$objSuporte = new SuporteClass();

if(isset($_REQUEST['replicar'])){
    $regiao  = $_REQUEST['rep_regiao'];
    $id_suporte = $_REQUEST['rep_suporte'];
    $replica = acentoMaiusculo($_REQUEST['replica']);
    $data = date('Y-m-d');
    
    $result = mysql_query("SELECT * FROM suporte where id_suporte = '{$id_suporte}'");
    $row = mysql_fetch_array($result);
    
    if($row['quant'] == '0'){
        $quant = '3';
    }else{
        $quant = $row['quant'] + 1;
    }
    
    //PREPARA O CONTEÚDO A SER GRAVADO
    $somecontent = "
                Data de abertura do chamado: {$row['ultima_alteracao']}
                <br><br>
                Mensagem:
                <br><br>
                <font color=blue>{$row['mensagem']}</font>
                <br><br>
                <b style='font-family:Arial, Helvetica, sans-serif; font-size:10; '><font color=#666666>
                Fim da mensagem</font></b>
                <br>---------------------------<br>
                Data da resposta: {$row['ultima_alteracao']}
                <br><br>
                Resposta:
                <br><br>
                <font color=orange>{$row['resposta']}</font>
                <br><br>
                <b style='font-family:Arial, Helvetica, sans-serif; font-size:10; '><font color=#666666>
                Fim da mensagem</font></b>
                <br>---------------------------<br>";
    
    //ARQUIVO TXT > DANDO UM NOME PARA O ARQUIVO E A LOCALIZAÇÃO
    $filename = "../../suporte/arquivos/historico_chamado_".$id_suporte.".txt";
    $filename2 = "/home/ispv/public_html/intranet/suporte/arquivos/historico_chamado_".$id_suporte.".txt";     
        
    // SE EXISTIR ELE VAI ABRIR O ARQUIVO E ESCREVER O CONTEÚDO NELE
    if (!$abrir = fopen($filename, "a+")) {
        echo "Erro abrindo arquivo ($filename)";
        exit;
    }
    
    //LIBERANDO ARQUIVO PARA ALTERAÇÕES
    chmod($filename2, 0777);
    
    //ESCREVE NO ARQUIVO TXT
    if (!fwrite($abrir, $somecontent)) {
        print "Erro escrevendo no arquivo ($filename2)";
        exit;
    }
    
    //FECHA O ARQUIVO 
    fclose($abrir);   
    
    mysql_query("UPDATE suporte SET mensagem = '{$replica}', resposta = '', ultima_alteracao = '{$data}', status = '3', quant = '{$quant}' WHERE id_suporte = '{$id_suporte}' ") or die(mysql_error());
    
    header("Location: index.php");
}

if(validate($_REQUEST['id_suporte'])){
    $id_suporte = validatePost('id_suporte');
    $suporte = $objSuporte->getSuporte($id_suporte);
}else{
    exit("erro fatal, não foi recebido o suporte");
}

// trata tipo de ocorrencia
switch($suporte['tipo']) {
    case 1:
    $ocorre = 'Informação';
    case 2:
    $ocorre = 'Reclamação';
    case 3:
    $ocorre = 'Inclusão';
    case 4:
    $ocorre = 'Exclusão';
    case 5:
    $ocorre = 'Erro';
    case 6:
    $ocorre = 'Sugestões';
    case 7:
    $ocorre = 'Alteração';
}

//trata anexo recebido
if(!empty($suporte['arquivo'])){
    $img = '<a href="../../suporte/arquivos/suporte_'.$suporte['id_regiao'].'_'.$suporte['id_suporte'].$suporte['arquivo'].'" rel="lightbox" title="Anexo" target="_blank">Abrir anexo</a>';
}else{
    $img = 'Sem Anexo';
}

//trata historico
if($suporte['quant'] != '0'){
    $nome_arquivo = 'historico_chamado_'.$suporte['id_suporte'].'.txt';
}else{
    $arquivo = NULL;
}

$_SESSION['voltar'] = $_REQUEST['status'];
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Suporte</title>
        
        <link rel="shortcut icon" href="favicon.ico" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-sistema-header"><h2><span class="glyphicon glyphicon-phone"></span> - Sistema</h2></div>                                                                          
            
            <form action="" method="post" class="form-horizontal top-margin1" id="form_suporte">
                
                <input type="hidden" name="home" id="home" value="" />
                
                <ul class="breadcrumb">
                    <li><a href="../../">Home</a></li>
                    <li><a href="javascript:;" data-key="1" data-nivel="../../" data-form="form_suporte" class="return_principal">Principal</a></li>
                    <li><a href="index.php">Suporte</a></li>
                    <li class="active">Visualização de Chamado</li>
                </ul>
                
                <fieldset>
                    <legend>Dados</legend>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="pull-right">
                                <span class="label label-<?php echo SuporteClass::convertPrioridadeClass($suporte['prioridade']); ?>"><?php echo SuporteClass::convertPrioridade($suporte['prioridade'])?></span>                            
                            </div>
                            <span class="panel-title"><?php echo $suporte['aberto_por']?></span>
                            <div class="panel-padding">
                                <p class="text-muted">Aberto em <?php echo $suporte['criado_em']?></p>
                            </div>
                        </div>                                                

                        <div class="panel-body">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>
                                            <label>Assunto</label>
                                            <p><?php echo $suporte['assunto']?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label>Tipo de ocorrência</label>
                                            <p><?php echo acentoMaiusculo($ocorre); ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label>Mensagem</label>
                                            <p><?php echo $suporte['mensagem']?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label>Resposta</label>
                                            <p><?php echo $suporte['resposta']?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label>Anexo Recebido</label>
                                            <p><?php echo $img; ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label>Histórico</label>
                                            <p>
                                                <?php
                                                $filename = "/home/ispv/public_html/intranet/suporte/arquivos/$nome_arquivo";
                                                $handle = fopen ($filename, "r");
                                                $conteudo = fread ($handle, filesize ($filename));
                                                print $conteudo;				
                                                fclose($handle); 
                                                ?>
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php if($suporte['status'] != 4){ ?>
                        <div class="panel-footer text-right">
                            <div class="col-md-offset-10">
                                <a class="btn btn-warning fim_chamado" data-key="<?php echo $suporte['id_suporte']; ?>"><span class="fa fa-power-off"></span>&nbsp;&nbsp;Finalizar Chamado</a>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    
                    <?php if($suporte['status'] == 2){ ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span class="panel-title">Replicar</span>
                        </div>
                        <div class="panel-body">
                            <textarea class="form-control validate[required]" rows="5" name="replica"></textarea>
                        </div>
                        <div class="panel-footer text-right">
                            <button type="submit" class="btn btn-primary" id="replicar" name="replicar">Enviar</button>
                        </div>
                    </div>
                    <?php } ?>
                </fieldset>
                <input type="hidden" name="rep_suporte" value="<?php echo $suporte['id_suporte']; ?>" />
                <input type="hidden" name="rep_regiao" value="<?php echo $suporte['id_regiao']; ?>" />
            </form>
            
            <button type="button" class="btn btn-default" id="volta_index">
                <span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar
            </button>
            
            <footer>
                <div class="row">
                    <div class="page-header"></div>
                    <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                    <div class="col-lg-12">
                        <p>Pay All Fast 3.0</p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </div>
            </footer>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/sistema/suporte.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script>
            $(function() {                
                $("#form_suporte").validationEngine({promptPosition : "topRight"});
            });
        </script>
    </body>
</html>