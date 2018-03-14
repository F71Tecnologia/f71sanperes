<?php
include('../../include/restricoes.php');
include('../../../conn.php');
include('../../../classes/global.php');
include("../../../wfunction.php");
include('../../../classes/PrestadorServicoClass.php');
$usuario = carregaUsuario();
//$Master = $usuario['id_master'];

$filtro = false;

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel" => "../../../", "key_btn" => "2", "area" => "Administrativo", "id_form" => "form1", "ativo" => "Layouts de contratos");
$breadcrumb_pages = array("Principal" => "../../../admin/index.php");

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

$res_layouts = PrestadorServico::getLayoutContratos();
$num_rows = mysql_num_rows($res_layouts);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$regiaoR = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : null;

if($_REQUEST['method'] == "exclui_layout"){
    $retorno = array("status" => "0");
    
    $id = $_REQUEST['id'];
    
    $desativa = PrestadorServico::delLayoutContrato($id);

    if($desativa){
        $retorno = array("status" => "1");
    }

    echo json_encode($retorno);
    exit;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: Layouts de contratos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../../favicon.ico">

        <!--Custom CSS-->
        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../../net1.css" rel="stylesheet" type="text/css" />
         <link href="../prestador.css" rel="stylesheet" type="text/css" />

        <!--Jquery-->
        <script src="../../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../../resources/js/bootstrap.js" ></script>
        <script src="../../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../../resources/js/bootstrap-dialog.min.js" ></script>
        <script src="../../../js/global.js" type="text/javascript"></script>
        <script src="../../../resources/js/main.js" ></script>
        <script type="text/javascript" src="../../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
        
        <!-- Bootstrap -->
        <!--<link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">-->
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/add-ons.min.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <!--<link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">-->
        <!--<link href="../../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">-->

        <script>
            $(function () {                
                $(".bt-image").on("click", function () {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    
                    //THICKBOX VISUALIZA DOCUMENTOS
                    if (action === "visualizar") {
                        $("#layout").val(key);
                        $("#form1").attr('action', 'contrato.php');
                        $("#form1").submit();
                    } else if (action === "editar") {
                        $("#layout").val(key);
                        $("#form1").attr('action', 'form_layout.php');
                        $("#form1").submit();
                    } else if (action === "excluir") {
                        BootstrapDialog.confirm('Deseja realmente excluir esse layout?', 'Confirmação de Exclusão', function(result) {
                            if (result) {
                                $.ajax({
                                    type: "post",
                                    url: "index.php",
                                    dataType: "json",
                                    data: {
                                        id: key,
                                        method: "exclui_layout"
                                    },
                                    success: function(data) {
                                        if(data.status == "1"){
                                            $("#layout_"+key).fadeOut();
                                        }
                                    }
                                });
                            }
                        },
                        'danger');
                    }
                });
                
                $("#novoContrato").click(function () {
                    $("#form1").attr('action', 'form_layout.php');
                    $("#form1").submit();
                });
            });
            function abre_processo(id) {
                $.post(window.location, {id: id, acao: 'abre_processo'}, function (data) {
                    console.log(data);
                }, 'json');
            }
        </script>

    </head>
    <body>
        
        <?php include("../../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO<small> - Layouts de contratos</small></h2></div>
                </div>
            </div>
            <div id="alert" style="background-color:#F30;color:#FFF;font-weight:bold; padding-left:3px;"></div>
            <form class="form-horizontal" action="" method="post" name="form1" id="form1" enctype="multipart/form-data">
                <input type="hidden" name="layout" id="layout" value="" />
                
                <div class="panel panel-default">
                    <div class="panel-heading">Lista de Layouts</div>                    

                    <div class="panel-footer hidden-print text-right controls">
                        <button type="submit" class="button btn btn-success" value="Novo Contrato" name="novo" id="novoContrato"><span class="fa fa-plus"></span> Novo Contrato</button>
                    </div>
                </div>

                <?php if ($num_rows > 0) { ?>
                    <br/>
                    <!--<p style="text-align: right; margin-top: 20px"><button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar Para Excel</button></p>-->
                    <table id="tbRelatorio" class="table table-striped table-hover text-sm valign-middle table-bordered" >
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Tipo de Serviço</th>                                
                                <th colspan="3">Açoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysql_fetch_assoc($res_layouts)) { ?>
                                <tr id="layout_<?php echo $row['id_layout_contrato']; ?>">
                                    <td><?php echo $row['id_layout_contrato']; ?></td>
                                    <td><?php echo $row['nome']; ?></td>
                                    <td><?php echo $row['tipo_servico']; ?></td>                                    
                                    <td class="center">
                                        <a class="btn btn-xs btn-primary bt-image" target="_blank" data-type="visualizar" data-key="<?php echo $row['id_layout_contrato']; ?>" title="Visualizar"><i class="fa fa-search"></i></a>                                        
                                    </td>
                                    <td class="center">
                                        <a class="btn btn-xs btn-warning bt-image" target="_blank" data-type="editar" data-key="<?php echo $row['id_layout_contrato']; ?>" title="Editar"><i class="fa fa-pencil"></i></a>                                        
                                    </td>
                                    <td class="center">
                                        <a class="btn btn-xs btn-danger bt-image" target="_blank" data-type="excluir" data-key="<?php echo $row['id_layout_contrato']; ?>" title="Excluir"><i class="fa fa-trash-o"></i></a>                                        
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>                            
                <?php } else { ?>
                    <br/>
                    <div id='message-box' class='message-yellow'>
                        <p>Nenhum registro encontrado</p>
                    </div>
                <?php } ?>
            </form>

            <?php include('../../../template/footer.php'); ?>

        </div>
    </body> 
</html>