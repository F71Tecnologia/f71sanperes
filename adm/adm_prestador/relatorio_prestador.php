<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../classes/global.php');
include("../../wfunction.php");
include('../../classes/PrestadorServicoClass.php');
include('../../classes_permissoes/acoes.class.php');
$usuario = carregaUsuario();
//$Master = $usuario['id_master'];

$filtro = false;
$objAcoes = new Acoes();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$nome_pagina = "Relatório de Prestadores";
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "2", "area" => "Administrativo", "id_form" => "form1", "ativo" => $nome_pagina);
$breadcrumb_pages = array("Prestador de Serviço" => "index.php");

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']))) {
    $filtro = true;
    
    $auxProjeto = ($_REQUEST['projeto']) ? " AND A.id_projeto = '{$_REQUEST['projeto']}' " : null;
    
    $sql = "SELECT 
    A.c_razao AS 'razao', 
    CONCAT(DATE_FORMAT(A.contratado_em, '%d/%m/%Y'), ' - ', DATE_FORMAT(A.encerrado_em, '%d/%m/%Y')) AS 'vigencia',
    CASE A.prestador_tipo
            WHEN 1 THEN 'Pessoa Jurídica'
            WHEN 2 THEN 'Pessoa Jurídica - Cooperativa'
            WHEN 3 THEN 'Pessoa Física'
            WHEN 4 THEN 'Pessoa Jurídica - Prestador de Serviço'
            WHEN 5 THEN 'Pessoa Jurídica - Administradora'
            WHEN 6 THEN 'Pessoa Jurídica - Publicidade'
            WHEN 7 THEN 'Pessoa Jurídica Sem Retenção'
            WHEN 9 THEN 'Pessoa Jurídica - Médico'
            ELSE 0
    END AS 'tipo_contrato',
    A.numero AS 'contrato',
    A.valor AS 'valor',
    D.nome AS 'associacao_financeira',
    B.nome AS 'regiao'
    FROM prestadorservico A
    LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
    LEFT JOIN nfse_codigo_servico_assoc C ON (A.id_prestador = C.id_prestador)
    LEFT JOIN entradaesaida D ON (C.id_tipo_entradasaida = D.id_entradasaida)
    WHERE A.status = 1 $auxProjeto
    ORDER BY A.contratado_em";
    $qry = mysql_query($sql);
    $num_rows = mysql_num_rows($qry);
    //cria matriz dividida por prestador_tipo
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: <?php echo $nome_pagina ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">

        <!--Custom CSS-->
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="prestador.css" rel="stylesheet" type="text/css" />

        <!--Jquery-->
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap.js" ></script>
        <script src="../../resources/js/bootstrap-dialog.min.js" ></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <!--<script src="../../resources/js/bootstrap-dialog.min.js" ></script>-->
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js" ></script>
        <script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>


        <!-- Bootstrap -->
        <!--<link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">-->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet" media="screen">
        <!--<link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">-->
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <!--<link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">-->
        <!--<link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">-->
    </head>
    <body>

        <?php include("../../template/navbar_default.php"); ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO<small> - <?php echo $nome_pagina ?></small></h2></div>
                </div>
            </div>
            <div id="alert" style="background-color:#F30;color:#FFF;font-weight:bold; padding-left:3px;"></div>
            <form class="form-horizontal" action="" method="post" name="form1" id="form1" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading">Filtro</div>
                    <div class="panel-body">
                        <div class="form-group">
<!--                            <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoR ?>" />
                            <input type="hidden" name="prestador" id="prestador" value="" />-->
<!--                            <label class="control-label col-sm-2 first">Região</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect(GlobalClass::carregaProjetosByMaster($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='form-control required[custom[select]]'") ?>
                            </div>-->
                            <label class="control-label col-sm-1 first">Projeto </label>
                            <div class="col-sm-4" >
                                <?php echo montaSelect(GlobalClass::carregaProjetosByMaster($usuario['id_master'], ['' => 'Todos']), $_REQUEST['projeto'], "id='projeto' name='projeto' class='form-control required[custom[select]]'") ?>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer hidden-print text-right controls">
                        <button type="submit" class="button btn btn-primary" value="Filtrar" name="filtrar"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>

                </div>


                <?php if ($filtro) { ?>
                    <?php if ($num_rows > 0) { ?>

                        <br/>
                        <p style="text-align: right; margin-top: 20px"><button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar Para Excel</button></p>
                        <table id="tbRelatorio" class="table table-striped table-hover text-sm valign-middle table-bordered" >
                            <thead>
                                <tr>
                                    <th>Razão Social</th>
                                    <th>Vigencia</th>
                                    <th>Tipo Contrato</th>
                                    <th>Contrato</th>
                                    <th>Valor</th>
                                    <th>Associação Financeira</th>
                                    <th>Regiao</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php while ($row = mysql_fetch_assoc($qry)) { ?>
                                <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                    <td><?php echo $row['razao']; ?></td>
                                    <td><?php echo $row['vigencia']; ?></td>
                                    <td><?php echo $row['tipo_contrato']; ?></td>
                                    <td><?php echo $row['contrato']; ?></td>
                                    <td><?php echo number_format(str_replace(',','.',$row['valor']), 2, ',', '.'); ?></td>
                                    <td><?php echo $row['associacao_financeira']; ?></td>
                                    <td><?php echo $row['regiao']; ?></td>
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
                <?php } ?>
            </form>

            <?php include('../../template/footer.php'); ?>

        </div>
    </body>
</html>
