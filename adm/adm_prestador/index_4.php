<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/PrestadorServicoClass.php');

$usuario = carregaUsuario();
$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarPrestador']))) {
    $filtro = true;
    if(isset($_SESSION['voltarPrestador'])){
        $_REQUEST['regiao'] = $_SESSION['voltarPrestador']['id_regiao'];
        $_REQUEST['projeto'] = $_SESSION['voltarPrestador']['id_projeto'];
        unset($_SESSION['voltarPrestador']);
    }
    
    $rs = montaQuery("prestadorservico", "*,DATE_FORMAT(contratado_em, '%d/%m/%Y') AS contratado_embr, DATE_FORMAT(encerrado_em, '%d/%m/%Y') AS encerrado_embr ", "id_regiao = {$_REQUEST['regiao']} AND id_projeto = {$_REQUEST['projeto']}", "prestador_tipo,c_razao", null, null, false);
    $num_rows = mysql_num_rows($rs);
    
    $query = "SELECT C.id_prestador, COUNT(B.prestador_documento_id) AS cnt
                FROM prestador_tipo_doc AS A
                LEFT JOIN prestador_documentos AS B ON (A.prestador_tipo_doc_id = B.prestador_tipo_doc_id)
                LEFT JOIN prestadorservico AS C ON (B.id_prestador = C.id_prestador)
                WHERE B.data_vencimento < CURDATE()
                AND id_regiao = {$_REQUEST['regiao']}
                AND id_projeto = {$_REQUEST['projeto']}
                GROUP BY C.id_prestador";
    $result = mysql_query($query);
    $i = 0;
    while($row = mysql_fetch_assoc($result)){
        $id_doc_vencido[$i] = $row['id_prestador'];
        $qtd_doc_vencido[$row['id_prestador']] = $row['cnt'];
        $i++;
    }
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$regiaoR = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : null;

?>
<html>
    <head>
        <title>:: Intranet :: Prestador de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="prestador.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");

                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var emp = $(this).parents("tr").find("td:first").next().html();
                    
                    //THICKBOX VISUALIZA DOCUMENTOS
                    if (action === "docs") {    
                        thickBoxIframe(emp, "actions.php", {prestador: key, method: "getDocs"}, "625-not", "500");
                    }else if (action === "duplicar") {
                        $("#prestador").val(key);
                        $("#form1").attr('action','duplicar_prestador.php');
                        $("#form1").submit();
                    }else if (action === "prestador") {
                        $("#prestador").val(key);
                        $("#form1").attr('action','ver_prestador.php');
                        $("#form1").submit();
                    }else if (action === "editar") {
                        $("#prestador").val(key);
                        $("#form1").attr('action','form_prestador_2.php');
                        $("#form1").submit();
                    }
                });
                
                $("#novoPrest").click(function(){
                    $("#form1").attr('action','form_prestador.php');
                    $("#form1").submit();
                });
            });
            function abre_processo(id){
                $.post(window.location,{id:id,acao:'abre_processo'},function(data){
                    console.log(data);
                },'json');
            }
        </script>
        <style>
            .bt-image{
                width: 18px;
                cursor: pointer;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Administrativo - Prestador de Serviço</h2>
                        <p>Administração geral das Empresas Prestadoras de Serviço</p>
                    </div>
                </div>

                <fieldset>
                    <legend>Filtro</legend>
                    <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoR ?>" />
                    <input type="hidden" name="prestador" id="prestador" value="" />
                    <p><label class="first">Região:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='required[custom[select]]'") ?></p>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1"=>"« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='required[custom[select]]'") ?></p>

                    <p class="controls"> <input type="submit" class="button" value="Filtrar" name="filtrar" /> <?php if ($filtro) { ?><input type="submit" class="button" value="Novo Prestador" name="novo" id="novoPrest" /><?php } ?></p>
                </fieldset>

                <?php
                if ($filtro) {
                    if ($num_rows > 0) {
                        $count = 0;
                        ?>
                        <br/>
                        <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                        <table id="tbRelatorio" cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Razão Social</th>
                                    <th>CNPJ</th>
                                    <th>Início</th>
                                    <th>Término</th>
                                    <th>Valor</th>
                                    <th>Quantidade Docs</th>
                                    <th>Qtd Docs Vencidos</th>
                                    <th>Contrato</th>
                                    <th>Docs</th>
                                    <th colspan="3">Açoes</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php while ($row = mysql_fetch_assoc($rs)) { ?>
                                    <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                        <td><?php echo $row['id_prestador']; ?></td>
                                        <td><?php echo $row['c_razao']; ?></td>
                                        <td><?php echo $row['c_cnpj']; ?></td>
                                        <td><?php echo $row['contratado_embr']; ?></td>
                                        <td><?php echo $row['encerrado_embr']; ?></td>
                                        <td>
                                            <?php
                                            if($row['valor'] > 0) {
                                                $convert_valor = str_replace(",",".",$row['valor']);                                                
                                                echo formataMoeda($convert_valor);
                                            }else{
                                                echo "";
                                            }
                                            ?>
                                        </td>                                        
                                        <td class="center <?php echo (array_search($row['id_prestador'],$id_doc_vencido))? 'back-red':'back-green' ;?>">
                                            <?php echo PrestadorServico::getStatusList($row['id_prestador']) ?>
                                        </td>
                                        <td class="center back-red"><?php echo $qtd_doc_vencido[$row['id_prestador']]; ?></td>
                                        <td class="center"><a href="gerenciar/?id=<?php echo $row['id_prestador']; ?>"><img src="../../imagens/icones/icon-doc.gif" title="Gerenciar" class="bt-image" /></a></td>
                                        <td class="center"><img src="../../imagens/icones/icon-docview.gif" title="Documentos" class="bt-image" data-type="docs" data-key="<?php echo $row['id_prestador']; ?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-doc.gif" title="Ver Prestador" class="bt-image" data-type="prestador" data-key="<?php echo $row['id_prestador']; ?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php echo $row['id_prestador']; ?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-copy.png" title="Duplicar" class="bt-image" data-type="duplicar" data-key="<?php echo $row['id_prestador']; ?>" /></td>
                                    </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?php } else { ?>
                        <br/>
                        <div id='message-box' class='message-yellow'>
                            <p>Nenhum registro encontrado</p>
                        </div>
                    <?php }
                } ?>
            </form>
        </div>
    </body>
</html>