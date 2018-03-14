<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');
include('../classes/PrestadorServicoClass.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"36", "area"=>"Prestação de Contas", "ativo"=>"Relação de Prestadores","id_form"=>"form1");

$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarPrestador']))) {
    $filtro = true;
    if (isset($_SESSION['voltarPrestador'])) {
        $_REQUEST['regiao'] = $_SESSION['voltarPrestador']['id_regiao'];
        $_REQUEST['projeto'] = $_SESSION['voltarPrestador']['id_projeto'];
        unset($_SESSION['voltarPrestador']);
    }

    $rs = montaQuery("prestadorservico", "*,DATE_FORMAT(contratado_em, '%d/%m/%Y') AS contratado_embr, DATE_FORMAT(encerrado_em, '%d/%m/%Y') AS encerrado_embr ", "id_regiao = {$_REQUEST['regiao']} AND status = 1 AND id_projeto = {$_REQUEST['projeto']} AND encerrado_em >= CURRENT_DATE()", "prestador_tipo,c_razao", null, null, false);
    $num_rows = mysql_num_rows($rs);
    
    //enc = contratos encerrados
    $rs_enc = montaQuery("prestadorservico", "*,DATE_FORMAT(contratado_em, '%d/%m/%Y') AS contratado_embr, DATE_FORMAT(encerrado_em, '%d/%m/%Y') AS encerrado_embr ", "id_regiao = {$_REQUEST['regiao']} AND status = 1 AND id_projeto = {$_REQUEST['projeto']} AND encerrado_em < CURRENT_DATE()", "c_razao", null, null, false);
    $num_rows_enc = mysql_num_rows($rs_enc);
        
    //cria matriz dividida por prestador_tipo
    while ($row1 = mysql_fetch_assoc($rs)) {
        $row_prestador[$row1['prestador_tipo']][$row1['id_prestador']] = $row1;
    }
    
    //Array com os tipos de contrato
    $arrTipos = array(
    "1" => "Pessoa Jurídica",
    "2" => "Pessoa Jurídica - Cooperativa",
    "3" => "Pessoa Física",
    "4" => "Pessoa Jurídica - Prestador de Serviço",
    "5" => "Pessoa Jurídica - Administradora",
    "6" => "Pessoa Jurídica - Publicidade",
    "7" => "Pessoa Jurídica Sem Retenção",
    "9" => "Pessoa Jurídica - Médico");


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
    while ($row = mysql_fetch_assoc($result)) {
        $id_doc_vencido[$i] = $row['id_prestador'];
        $qtd_doc_vencido[$row['id_prestador']] = $row['cnt'];
        $i++;
    }
    
    $query_enc = "SELECT C.id_prestador, COUNT(B.prestador_documento_id) AS cnt
        FROM prestador_tipo_doc AS A
        LEFT JOIN prestador_documentos AS B ON (A.prestador_tipo_doc_id = B.prestador_tipo_doc_id)
        LEFT JOIN prestadorservico AS C ON (B.id_prestador = C.id_prestador)
        WHERE B.data_vencimento < CURDATE()
        AND id_regiao = {$_REQUEST['regiao']}
        AND id_projeto = {$_REQUEST['projeto']}
        GROUP BY C.id_prestador";
        
    $result_enc = mysql_query($query_enc);
    $i_enc = 0;
    while ($row_enc = mysql_fetch_assoc($result_enc)) {
        $id_doc_vencido_enc[$i_enc] = $row_enc['id_prestador'];
        $qtd_doc_vencido_enc[$row_enc['id_prestador']] = $row_enc['cnt'];
        $i_enc++;
    }
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$regiaoR = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : null;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: Prestador de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.png" />                                                        
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>        
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $("#regiao").ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");

                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var emp = $(this).parents("tr").find("td:first").next().html();
                    
                    //THICKBOX VISUALIZA DOCUMENTOS
                    if (action === "docs") {
                        thickBoxIframe(emp, "actions.php", {prestador: key, method: "getDocs"}, "625-not", "500");
                    } else if (action === "duplicar") {
                        $("#prestador").val(key);
                        $("#form1").attr('action', 'duplicar_prestador.php');
                        $("#form1").submit();
                    } else if (action === "prestador") {
                        $("#prestador").val(key);
                        $("#form1").attr('action', 'ver_prestador.php');
                        $("#form1").submit();
                    } else if (action === "editar") {
                        $("#prestador").val(key);
                        $("#form1").attr('action', 'form_prestador.php');
                        $("#form1").submit();
                    }
                });
                
                $("#novoPrest").click(function() {
                    $("#form1").attr('action', 'form_prestador.php');
                    $("#form1").submit();
                });
            });
            function abre_processo(id) {
                $.post(window.location, {id: id, acao: 'abre_processo'}, function(data) {
                    console.log(data);
                }, 'json');
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
        
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-contas-header"><h2><span class="glyphicon glyphicon-list-alt"></span> - Prestação de Contas</h2></div>
        
            <div id="content">
                <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" class="form-horizontal top-margin1" >                    
                    
                    <input type="hidden" name="home" id="home" value="" />
                    <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoR ?>" />
                    <input type="hidden" name="prestador" id="prestador" value="" />
                    
                    <fieldset>
                        <legend>Administração geral das Empresas Prestadoras de Serviço</legend>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Região</label>
                            <div class="col-lg-4">                                
                                <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto</label>
                            <div class="col-lg-4">                                
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>                                                                
                        <div class="form-group">
                            <div class="pull-right">
                                <input type="submit" name="filtrar" value="Filtrar" class="btn btn-primary" />                            
                            </div>
                        </div>
                    </fieldset>                                        

                    <?php
                    if ($filtro) {
                        if ($num_rows > 0) {
                            $count = 0;
                            foreach ($row_prestador as $key => $value) {
                                ?>
                                <br/>
                                
                                <p style="text-align: right; margin-top: 20px">                                    
                                    <button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" class="btn btn-success exportarExcel"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
                                </p>
                                
                                <table id="tbRelatorio" class="grid table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th style="text-align:center; font-size: 2em;" colspan="13" class="active"><?= $key." - ".$arrTipos[$key] ?></th>
                                        </tr>
                                        <tr>
                                            <th>#</th>
                                            <th>Razão Social</th>
                                            <th>CNPJ</th>
                                            <th>Início</th>
                                            <th>Término</th>
                                            <th>Valor</th>
                                            <th>Quantidade Docs</th>
                                            <th>Qtd Docs Vencidos</th>                                        
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($value as $row) { ?>
                                            <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                                <td><?php echo $row['id_prestador']; ?></td>
                                                <td><?php echo $row['c_razao']; ?></td>
                                                <td><?php echo $row['c_cnpj']; ?></td>
                                                <td><?php echo $row['contratado_embr']; ?></td>
                                                <td><?php echo $row['encerrado_embr']; ?></td>
                                                <td>
                                                    <?php
                                                    if ($row['valor'] > 0) {
                                                        $convert_valor = str_replace(",", ".", $row['valor']);
                                                        echo formataMoeda($convert_valor);
                                                    } else {
                                                        echo "";
                                                    }
                                                    ?>
                                                </td>
                                                <td class="center <?php echo (array_search($row['id_prestador'], $id_doc_vencido)) ? 'back-red' : 'back-green'; ?>">
                                                    <?php
                                                    $doc_tot = PrestadorServico::getStatusList($row['id_prestador']);
                                                    echo $doc_tot;
                                                    ?>
                                                </td>
                                                <td class="center back-red">
                                                    <?php
                                                    if($doc_tot == '0'){
                                                        echo '0';
                                                    }else{
                                                        echo PrestadorServico::getDocsVencidos($row['id_prestador']);
                                                    }
                                                    ?>
                                                </td>                                            
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>                            
                                <?php }
                        }?>

                        <?php if($num_rows_enc > 0){ ?>	
                            <br/>
                            <p style="text-align: right; margin-top: 20px">                                
                                <button type="button" onclick="tableToExcel('tbRelatorio_enc', 'Relatório')" class="btn btn-success exportarExcel"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
                            </p>
                            
                            <table id="tbRelatorio_enc" class="grid table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th style="text-align:center; font-size: 2em;" colspan="13" class="active">Contratos encerrados</th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Razão Social</th>
                                        <th>CNPJ</th>
                                        <th>Início</th>
                                        <th>Término</th>
                                        <th>Valor</th>
                                        <th>Quantidade Docs</th>
                                        <th>Qtd Docs Vencidos</th>                                    
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row_enc = mysql_fetch_array($rs_enc)) { ?>
                                    <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                        <td><?php echo $row_enc['id_prestador']; ?></td>
                                        <td><?php echo $row_enc['c_razao']; ?></td>
                                        <td><?php echo $row_enc['c_cnpj']; ?></td>
                                        <td><?php echo $row_enc['contratado_embr']; ?></td>
                                        <td><?php echo $row_enc['encerrado_embr']; ?></td>
                                        <td>
                                            <?php
                                            if ($row_enc['valor'] > 0) {
                                                $convert_valor_enc = str_replace(",", ".", $row_enc['valor']);
                                                echo formataMoeda($convert_valor_enc);
                                            } else {
                                                echo "";
                                            }
                                            ?>
                                        </td>
                                        <td class="center <?php echo (array_search($row_enc['id_prestador'], $id_doc_vencido_enc)) ? 'back-red' : 'back-green'; ?>">
                                            <?php
                                            $doc_tot_enc = PrestadorServico::getStatusList($row_enc['id_prestador']);
                                            echo $doc_tot_enc;
                                            ?>
                                        </td>
                                        <td class="center back-red">
                                            <?php
                                            if($doc_tot_enc == '0'){
                                                echo '0';
                                            }else{
                                                echo PrestadorServico::getDocsVencidos($row_enc['id_prestador']);
                                            }
                                            ?>
                                        </td>                                    
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>                                                        
                        <?php } ?> 

                        <?php if($num_rows == 0 && $num_rows_enc == 0) {?>
                            <br/>
                            <div class='alert alert-warning'>
                                <p>Nenhum registro encontrado</p>
                            </div>
                        <?php }
                    }
                    ?>
                </form>
                
                <?php include_once '../template/footer.php'; ?>
                
            </div>
        </div>
    </body>
</html>