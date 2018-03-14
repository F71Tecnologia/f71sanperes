<?php
include('include/restricoes.php');
include('../funcoes.php');
include('include/criptografia.php');
include('../classes/formato_data.php');

$prestador = mysql_real_escape_string($_GET['prestador']);
$tipo_doc_id = mysql_real_escape_string($_GET['tp']);

$qr_empresa = mysql_query("SELECT * FROM prestadorservico WHERE id_prestador = '$prestador'");
$row_prestador = mysql_fetch_assoc($qr_empresa);

$qr_doc = mysql_query("SELECT * FROM prestador_documentos WHERE prestador_tipo_doc_id = '$tipo_doc_id' AND id_prestador = '$prestador' ORDER BY data_vencimento DESC");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-5589-1" />
        <title>Untitled Document</title>
        <link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript" ></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript" ></script>
        <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../js/global.js" type="text/javascript" ></script>
        <style>
            body{
                font-size: 12px;
            }
            .bt-image{
                padding: 0 10px;
            }
            .marcado{
                background-color: #EAF4FF;
                border: solid 1px #CBE2FF;
            }
            #edicao{
                width: 100%;
                text-align: center;
                padding: 10px 0;
            }
        </style>
        <script>
            $(function(){
                $(".data").mask("99/99/9999");
                
                $(".bt-image").click(function(){
                    var li = $(this).parents("li")
                    var id = li.attr('data-key');
                    
                    var acao = $(this).attr('data-tp');
                    if(acao == "editar"){
                        $(".marcado").removeClass("marcado");
                        li.addClass("marcado");
                        var dt = $("span",li).html();
                        $("#edicao").removeClass("hidden");
                        
                        $("#id_edit").val(id);
                        $("#edit_data").val(dt);
                    }else{
                        if(confirm("Essa acao e irreversivel, deseja realmente apagar esse documento?")){
                            $.post("actions.prestadorservico.php",{id: id, method: "excluirDoc"},function(){
                                $("#fancybox-close").trigger("click");
                                history.go(-1);
                            },"json");
                        }
                    }
                });
                
                $("#bt-cancel").click(function(){
                    $("#edicao").addClass("hidden");
                    $(".marcado").removeClass("marcado");
                });
                
                $("#bt-salvar").click(function(){
                    showLoading($("#edit_data"),"../");
                    var idEd = $("#id_edit").val();
                    var novaDt = $("#edit_data").val();
                    
                    $.post("actions.prestadorservico.php",{id: idEd, valor: novaDt, method: "editaData"},function(data){
                        if(data.status != "1"){
                            alert("Erro ao alterar a data");
                        }
                        
                        removeLoading();
                        $("#edicao").addClass("hidden");
                        $(".marcado").removeClass("marcado");
                        $("li[data-key="+idEd+"] p span").html(novaDt);
                    },"json");
                    
                });
            });
        </script>
    </head>
    <body>
        <div id="corpo">
            <div id="conteudo">
                <h3><?php echo utf8_encode($row_prestador['c_razao']) ?></h3>
                <p>Documentos Vinculados</p>
                <hr/>
                <ul>
                <?php
                while ($row = mysql_fetch_assoc($qr_doc)):
                    echo "<li style='padding: 10px 0;' data-key='{$row['prestador_documento_id']}'><p><span>";
                    echo implode('/', array_reverse(explode('-', $row['data_vencimento'])));
                    echo "</span> - <a href=\"prestador_documentos/{$row['nome_arquivo']}{$row['extensao_arquivo']}\" target='_blanck'> Baixar Documento</a> ";
                    echo " <a href=\"javascript:;\" class='bt-image' data-tp='editar'> <img src='../imagens/icon-edit.gif' alt='Editar' title='Editar' /> </a> ";
                    echo " <a href=\"javascript:;\" class='bt-image' data-tp='excluir'> <img src='../imagens/icon-excluir.png' alt='Excluir' title='Excluir' /> </a> ";
                    echo '</p></li>';
                endwhile;
                ?></ul>
            </div>
            
            <div id="edicao" class="hidden">
                <input type="hidden" name="id_edit" id="id_edit" value="" />
                <p>Data de Vencimento: <input type="text" name="edit_data" id="edit_data" class="data" value="" size="12" /> <input type="button" name="salvar" id="bt-salvar" value="Salvar"/> <input type="button" name="cancel" id="bt-cancel" value="Cancelar"/></p>
            </div>
        </div>
    </body>
</html>