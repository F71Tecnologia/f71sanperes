<?php
session_start();
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}
include("../../conn.php");
include("../../wfunction.php");

$usuario = carregaUsuario();

$id_clt = isset($_REQUEST['clt']) ? $_REQUEST['clt'] : NULL;
$id_doc = isset($_REQUEST['id_doc']) ? $_REQUEST['id_doc'] : NULL;
$id_regiao = isset($_REQUEST['id_reg']) ? $_REQUEST['id_reg'] : NULL;
$id_projeto = isset($_REQUEST['pro']) ? $_REQUEST['pro'] : NULL;

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'removeAnexo'){
    $nome_doc = $_REQUEST['nome_doc'];
    $caminho = 'arquivos_advertencia/'.$nome_doc.'.pdf';
    if (file_exists($caminho) and !empty($nome_doc)){
        unlink($caminho);
        
    }
    exit;
}

$qr_advertencias = "select *, DATE_FORMAT(data, '%d/%m/%Y') as dataBR from rh_doc_status where id_clt = $id_clt and tipo =10 order by dataBR desc";
$result_advertencias = mysql_query($qr_advertencias);
$qr_suspensao = "select *, DATE_FORMAT(data, '%d/%m/%Y') as dataBR from rh_doc_status where id_clt = $id_clt and tipo =9 order by dataBR desc";
$result_suspensao = mysql_query($qr_suspensao);

$result_regiao = mysql_query(" SELECT * FROM regioes WHERE id_regiao = '$id_regiao' ");
$row_regiao = mysql_fetch_array($result_regiao); 

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Medidas Disciplinares");
$breadcrumb_pages = array(
    "Lista Projetos" => "../../ver2.php", 
    "Visualizar Projeto" => "../../ver2.php?projeto={$id_projeto}", 
    "Lista Participantes" => "../../bolsista2.php?projeto={$id_projeto}", 
    "Visualizar Participante" => "../ver_clt2.php?pro={$id_projeto}&clt={$id_clt}"
); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Medidas Disciplinares</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS - <small>Medidas Disciplinares</small></h2></div>
                </div>
            </div>
            <form action="advertencia_form2.php?clt=<?php echo $id_clt ?>&pro=<?php echo $id_projeto ?>&id_reg=<?php echo $id_regiao ?>" method="post" name="form1" id="form1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Medidas Disciplinares Anteriores</h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-condensed table-hover">
                            <thead class="bg-primary">
                                <tr>
                                    <th>Motivo da advertência:</th>
                                    <th>Data da Medida Disciplinare</th>
                                    <th colspan="3">Ações</th>                              
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                while($row_advertencias = mysql_fetch_assoc($result_advertencias)) {
                                    $motivo = (strlen($row_advertencias['motivo']) > 30) ? substr($row_advertencias['motivo'], 0, 27)."..." : $row_advertencias['motivo'];
                                    if(empty($motivo)) {$motivo = "Cadastrado sem motivo";} ?>
                                    <tr class="<?php echo $class ?>">
                                        <td><a class="bt-view" title='<?php echo $row_advertencias['motivo'] ?>' href='javasctipt:;' data-key="<?php echo $row_advertencias['id_doc'] ?>"><?php echo $motivo ?></a></td>
                                        <td><?php echo $row_advertencias['dataBR'] ?></td>
                                        <td><a class="bt-remove" href="advertencia_remover.php?id_doc=<?php echo $row_advertencias['id_doc'] ?>&clt=<?php echo $id_clt ?>" ><img src="../../imagens/icones/icon-excluir.png" title="Remover advertência" data-tipo ="'.$row_advertencias['id_doc'].'_'.$id_clt.'"/></a></td>
                                        <td>
                                            <?php 
                                            $filename = 'arquivos_advertencia/'.$row_advertencias['id_doc'].'_'.$id_clt.'.pdf';
                                            if (file_exists($filename)) {?>
                                                <a href="<?=$filename?>"><img src="../../imagens/icones/icon-docview.gif" title="Visualizar advertência"/></a>
                                            <?php }?>
                                        </td>
                                        <td>
                                            <?php 
                                            if (!file_exists($filename)) {?>
                                                <input id="attachment" type="file" size="45" name="attachment[]" />
                                                <input type="button" name="enviar" class="enviar" value="Enviar" data-tipo="<?php echo $row_advertencias['id_doc'].'_'.$id_clt; ?>"/>
                                            <?php }else{
                                                echo '<img class="removeArquivo" src="../../imagens/icones/icon-trash.gif" title="Remover Anexo" data-tipo ="'.$row_advertencias['id_doc'].'_'.$id_clt.'"/>';
                                            } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-body">
                        <table class="table table-condensed table-hover">
                            <thead class="bg-default">
                                <tr>
                                    <th>Motivo da suspensão:</th>
                                    <th>Data da Medida Disciplinare</th>
                                    <th colspan="3">Ações</th>                              
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row_suspensao = mysql_fetch_assoc($result_suspensao)) {
                                    $motivo = (strlen($row_suspensao['motivo']) > 30) ? substr($row_suspensao['motivo'], 0, 27)."..." : $row_suspensao['motivo'];
                                    if(empty($motivo)) {$motivo = "Cadastrado sem motivo";} ?>
                                <tr class="<?php echo $class ?>">
                                    <td><a class="bt-view" title='<?php echo $row_suspensao['motivo'] ?>' href='javasctipt:;' data-key="<?php echo $row_suspensao['id_doc'] ?>"><?php echo $motivo ?></a></td>
                                    <td><?php echo $row_suspensao['dataBR'] ?></td>
                                    <td><a class="bt-remove" href="advertencia_remover.php?id_doc=<?php echo $row_suspensao['id_doc'] ?>&clt=<?php echo $id_clt ?>" ><img src="../../imagens/icones/icon-excluir.png" title="Remover advertência" data-tipo ="'.$row_suspensao['id_doc'].'_'.$id_clt.'"/></a></td>
                                    <td>
                                        <?php 
                                            $filename = 'arquivos_advertencia/'.$row_suspensao['id_doc'].'_'.$id_clt.'.pdf';
                                                if (file_exists($filename)) {?>
                                                    <a href="<?=$filename?>"><img src="../../imagens/icones/icon-docview.gif" title="Visualizar advertência"/></a>
                                          <?php }?>

                                    </td>
                                    <td>
                                        <?php 
                                        if (!file_exists($filename)) {?>
                                            <input id="attachment" type="file" size="45" name="attachment[]" />
                                            <input type="button" name="enviar" class="enviar" value="Enviar" data-tipo="<?php echo $row_suspensao['id_doc'].'_'.$id_clt; ?>"/>
                                        <?php }else{
                                            echo '<img class="removeArquivo" src="../../imagens/icones/icon-trash.gif" title="Remover Anexo" data-tipo ="'.$row_suspensao['id_doc'].'_'.$id_clt.'"/>';
                                        }
                                        ?>

                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="acao" id="acao" value="" />
                        <input type="hidden" name="id_doc" id="id_doc" value="" />
                        <input type="hidden" name="id_clt" id="id_clt" value="<?=$id_clt?>" />
                        <input type="hidden" name="id_projeto" id="id_projeto" value="<?=$id_projeto?>" />
                        <input type="hidden" name="id_regiao" id="id_regiao" value="<?=$id_regiao?>" />
                        <input type="hidden" name="nome_doc" id="nome_doc" value="" />
                        <?php echo $_SESSION['MESSAGE']; unset($_SESSION['MESSAGE']); ?>
                        
                        <input type="submit" class="btn btn-primary" name="cadastrar" id="cadastrar" value="Cadastrar Medida Disciplinar">
                    </div>
                </div>
            </form>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $(".bt-view").click(function() {
                    var id_doc = $(this).attr('data-key');
                    $("#id_doc").val(id_doc);
                    $("#acao").val(2);
                    $("#form1").submit();
                    return false;
                });
                
                $(".enviar").click(function (){
                    var tipo = $(this).data('tipo');
                    $("#nome_doc").val(tipo);
                    $("#form1").attr('action', 'enviar_arquivo.php');
                    $("#form1").attr('enctype', 'multipart/form-data');
                   
                    $("#form1").submit();
                });

                $(".removeArquivo").on("click",function(){
                    var tipo = $(this).data('tipo');
                    $.post("advertencia.php", {method: 'removeAnexo', nome_doc: tipo}, function() {
                        location.reload();
                    });
                });
            });
        </script>
    </body>
</html>
