<?php
session_start();
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}
include "../../conn.php";
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

$qr_advertencias = "select *, DATE_FORMAT(data, '%d/%m/%Y') as dataBR from rh_doc_status where id_clt = $id_clt and tipo = 10 order by dataBR desc";
$result_advertencias = mysql_query($qr_advertencias);

$result_regiao = mysql_query(" SELECT * FROM regioes WHERE id_regiao = '$id_regiao' ");
$row_regiao = mysql_fetch_array($result_regiao); ?>
<html>
    <head>
        <title>:: Intranet :: Advertências</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>


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
                    $.post("zadvertencia.php", {method: 'removeAnexo', nome_doc: tipo}, function() {
                        location.reload();
                    });
                });
            });
        </script>
        <style>
            .data{width: 80px;}
            .colEsq{
                float: left;
                width: 55%;
                margin-top: -10px;
            }
            fieldset{
                margin-top: 10px;
            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }
            .first{
                vertical-align: 0!important;
            }
            .first-2{
                vertical-align: 0!important;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content">
            <form action="advertencia_form.php?clt=<?php echo $id_clt ?>&pro=<?php echo $id_projeto ?>&id_reg=<?php echo $id_regiao ?>" method="post" name="form1" id="form1">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $row_regiao['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Advertências</h2>
                    </div>
                </div>
                
                <fieldset>
                    <legend>Advertências Anteriores</legend>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" style="width: 98%; margin: 10px;">
                        <thead>
                            <tr>
                                <th>Motivo da advertência:</th>
                                <th>Data da Advertência</th>
                                <th colspan="3">Ações</th>                              
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row_advertencias = mysql_fetch_assoc($result_advertencias)) {
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
                                    }
                                    ?>
                                    
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </fieldset>
                
                <input type="hidden" name="acao" id="acao" value="" />
                <input type="hidden" name="id_doc" id="id_doc" value="" />
                <input type="hidden" name="id_clt" id="id_clt" value="<?=$id_clt?>" />
                <input type="hidden" name="id_projeto" id="id_projeto" value="<?=$id_projeto?>" />
                <input type="hidden" name="id_regiao" id="id_regiao" value="<?=$id_regiao?>" />
                <input type="hidden" name="nome_doc" id="nome_doc" value="" />
                <?php echo $_SESSION['MESSAGE'];
                        unset($_SESSION['MESSAGE']); ?>
                <p class="controls">
                    <input type="submit" name="cadastrar" id="cadastrar" value="Cadastrar Advertência">
                </p>
            </form>
        </div>
    </body>
</html>