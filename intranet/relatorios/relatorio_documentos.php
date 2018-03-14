<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}  

include "../conn.php";
include "../classes/funcionario.php"; 
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
$optRegiao = getRegioes();
$ACOES = new Acoes();

$opt = array("2"=>"CLT","1"=>"Aut�nomo","3"=>"Cooperado","4"=>"Aut�nomo/PJ");

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "getList") {
    $id = $_REQUEST['id'];
    $contratacao = $_REQUEST['contratacao'];
    $html = "";
    
    if($contratacao == 2) {
        $sql_documento_anexados = "SELECT A.id_upload, A.arquivo 
                            FROM upload AS A
                            LEFT JOIN documento_clt_anexo AS B
                            ON B.id_upload = A.id_upload
                            WHERE B.id_clt = '{$id}'
                            AND B.anexo_status = 1
                            ORDER BY A.id_upload";
        $result_documento_anexados = mysql_query($sql_documento_anexados);
        
        $html .= "<h3>ANEXADOS<h3>";
        $html .= "<table class='table table-striped table-bordered table-bordered table-condensed table-hover text-sm valign-middle'>";
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th>#</th>";
        $html .= "<th>DOCUMENTO</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        while($row_documento_anexados = mysql_fetch_assoc($result_documento_anexados)) {
            $html .= '<tr>';
            $html .= "<td> {$row_documento_anexados['id_upload']} </td>";
            $html .= "<td>{$row_documento_anexados['arquivo']}</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        
        $sql_documento_nao_anexados = "SELECT id_upload, arquivo 
                            FROM upload
                            WHERE id_upload NOT IN (SELECT id_upload FROM documento_clt_anexo WHERE id_clt = '{$id}')
                            ORDER BY id_upload";
        $result_documento_nao_anexados = mysql_query($sql_documento_nao_anexados);
        
        $html .= "<h3>N�O ANEXADOS<h3>";
        $html .= "<table class='table table-striped table-bordered table-bordered table-condensed table-hover text-sm valign-middle'>";
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th>#</th>";
        $html .= "<th>DOCUMENTO</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        while($row_documento_nao_anexados = mysql_fetch_assoc($result_documento_nao_anexados)) {
            $html .= '<tr>';
            $html .= "<td> {$row_documento_nao_anexados['id_upload']} </td>";
            $html .= "<td>{$row_documento_nao_anexados['arquivo']}</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        
        echo utf8_encode($html);
        exit;
    } else {
        $sql_documento_anexados = "SELECT A.id_upload, A.arquivo 
                            FROM upload AS A
                            LEFT JOIN documento_autonomo_anexo AS B
                            ON B.id_upload = A.id_upload
                            WHERE B.id_autonomo = '{$id}'
                            AND B.anexo_status = 1
                            ORDER BY A.id_upload";
        $result_documento_anexados = mysql_query($sql_documento_anexados);
        
        $html .= "<h3>ANEXADOS<h3>";
        $html .= '<table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">';
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th>#</th>";
        $html .= "<th>DOCUMENTO</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        while($row_documento_anexados = mysql_fetch_assoc($result_documento_anexados)) {
            $html .= '<tr>';
            $html .= "<td> {$row_documento_anexados['id_upload']} </td>";
            $html .= "<td>{$row_documento_anexados['arquivo']}</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        
        $sql_documento_nao_anexados = "SELECT id_upload, arquivo 
                            FROM upload
                            WHERE id_upload NOT IN (SELECT id_upload FROM documento_autonomo_anexo WHERE id_autonomo = '{$id}')
                            AND id_upload NOT IN (6,7,8,11,12,13,14,15,16,17,18,19,22,23)
                            ORDER BY id_upload";
        $result_documento_nao_anexados = mysql_query($sql_documento_nao_anexados);
        
        $html .= "<h3>N�O ANEXADOS<h3>";
        $html .= '<table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">';
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th>#</th>";
        $html .= "<th>DOCUMENTO</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        while($row_documento_nao_anexados = mysql_fetch_assoc($result_documento_nao_anexados)) {
            $html .= '<tr>';
            $html .= "<td> {$row_documento_nao_anexados['id_upload']} </td>";
            $html .= "<td>{$row_documento_nao_anexados['arquivo']}</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        
        echo utf8_encode($html);
        exit;
    }
}

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    $contratacao = ($tipo_contratacao == "2")? "clt" : "autonomo";
    
    if($tipo_contratacao == 2) {
        $str_qr_relatorio = "SELECT A.nome, A.id_curso, A.id_clt AS id, B.nome AS nome_curso, B.salario 
            FROM rh_clt AS A
            LEFT JOIN curso AS B
            ON B.id_curso = A.id_curso
            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '$tipo_contratacao' ";
    }
    else {
        $str_qr_relatorio = "SELECT A.nome,A.id_curso, A.id_autonomo AS id, B.nome AS nome_curso, B.salario
            FROM autonomo AS A
            INNER JOIN curso AS B
            ON B.id_curso = A.id_curso 
            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '$tipo_contratacao' ";
    }
    if(!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }
    
    $str_qr_relatorio .= "ORDER BY A.nome";
    
    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$optSel = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : null;

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relat�rio de Documentos</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

        
        
        
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relat�rio de Documentos</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relat�rio</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >Regi�o</label>
                            <div class="col-sm-5">
                              <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        
                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-2">
                              <?php echo montaSelect(array("-1" => "� Selecione o Projeto �"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                        
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >Tipo Contrato</label>
                            <div class="col-sm-3">
                              <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>                          
                    </div>                          
                            
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo j� existente!'; ?></span>
                        
                        <div class="panel-footer text-right hidden-print controls">
                            <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Relat�rio de Documentos')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <?php } ?>
                            <?php ///permiss�o para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                                if($ACOES->verifica_permissoes(85)) { ?>
                                <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                            <?php } ?>
                                <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>                            
                        </div>
                    </div>
                    
                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                <table class="table table-striped table-hover table-condensed table-bordered text-sm valign-middle" id="tbRelatorio">
                    <thead>
                            <tr>
                                <th colspan="4"><?php echo $projeto['nome'] ?></th>
                            </tr>
                            <tr>
                                <th>NOME</th>
                                <th>FUN��O</th>
                                <th>SAL�RIO</th>   
                                <th>DOCUMENTOS</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome'] ?></td>
                                <td> <?php echo $row_rel['nome_curso']; ?></td>
                                <td align="center"><?php echo number_format($row_rel['salario'],2,',','.'); ?></td>            
                                <td class="text-center"><span title="Documentos" class="fa fa-file-text-o pointer documentos" data-id="<?php echo $row_rel['id']; ?>" data-contratacao="<?php echo $tipo_contratacao; ?>"></span></td>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                </table>
                <?php  } ?>
        
            </form>
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script>
          $(function() {
                $(".documentos").on("click", function() {
                    var id = $(this).data("id");
                    var contratacao = $(this).data("contratacao");
                    var nome = $(this).parents("tr").find("td:first").html();
                    thickBoxIframe(nome, "relatorio_documentos.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
                });
            });
            $(function() {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
        </script>
        
    </body>
</html>
