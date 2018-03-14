<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include("../conn.php");
include("../classes/funcionario.php");
include("../wfunction.php");
include('../classes/global.php');
include '../classes_permissoes/regioes.class.php';
include "../classes_permissoes/acoes.class.php"; 

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$opt = array("2" => "CLT", "1" => "Autônomo", "3" => "Cooperado", "4" => "Autônomo/PJ");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    $contratacao = ($tipo_contratacao == "2") ? "clt" : "autonomo";

    if ($tipo_contratacao == 2) {
        $str_qr_relatorio = "SELECT id_clt AS id, nome, pis
            FROM rh_clt
            WHERE id_regiao = '$id_regiao' AND status = '10' ";
    } else {
        $str_qr_relatorio = "SELECT id_autonomo AS id, nome, pis
            FROM autonomo
            WHERE id_regiao = '$id_regiao' AND tipo_contratacao = '$tipo_contratacao' AND status = '1' ";
    }
    if (!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND id_projeto = '$id_projeto' ";
    }

    $str_qr_relatorio .= "ORDER BY nome";

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

        <title>:: Intranet :: Relatório de Documentos</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Documentos </h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                        <div class="panel-body">
                            <div class="form-group" >
                                <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                                <div class="col-sm-5">
                                    <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?><span class="loader"></span>
                                </div>

                                <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                                <div class="col-sm-3">
                                    <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span> 
                                </div>
                            </div>
                                
                            <div class="form-group">
                                <label for="select" class="col-sm-2 control-label hidden-print" >Tipo de Contratação</label>
                                <div class="col-sm-2">
                                    <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo', 'class' => 'form-control')); ?><span class="loader"></span>
                                </div>

                                <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                            </div>
                        </div>

                        <div class="panel-footer text-right hidden-print controls">
                            <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <?php } ?>
                            <?php
                            ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                            if ($ACOES->verifica_permissoes(85)) {
                                ?>
                                <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                            <?php } ?>
                                <button type="submit" name="gerar" id="gerar" value="filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>
                    </div> 

                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                    <table class="table table-striped table-hover text-sm valign-middle table-bordered" id="tbRelatorio">
                       
                    <?php if($optSel == 1) { ?>
                        <thead>
                           <tr>
                                <th>NOME</th>
                                <th>CONTRATO</th>
                                <th>DISTRATO</th>
                                <th>TV SORRINDO</th>
                                <th>DECLARAÇÃO DE RENDA</th>
                                <th>CERTIFICADO</th>
                                <th>2ª VIA DE CONTRATO</th>
                                <th>ENCAMINHAMENTO BANCÁRIO</th>
                            </tr> 
                        </thead>

                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                            $class = ($cont++ % 2 == 0) ? "even" : "odd";
                            ?>

                            <tbody>
                                <tr class="<?php echo $class ?>">
                                  <td><?php echo $row_rel['nome']; ?></td>
                                <?php $qr_docs_autonomo = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '1' ORDER BY id_doc ASC");
				       while($docs_autonomo = mysql_fetch_assoc($qr_docs_autonomo)) {
                                           $qr_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '{$docs_autonomo['id_doc']}' AND id_clt = '{$row_rel['id']}'");  $verifica = mysql_num_rows($qr_verifica);
					   if (!empty($verifica)) {
//                                                $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
                                                $img = '<div class="text-center success"><span class="fa fa-check"></span></div>';
                                            } else {
//                                                $img = '<img src="../imagens/naoassinado.gif" width="15" height="17">';
                                                $img = '<div class="text-center danger"><span class="fa fa-times"></span></div>';
                                            }
                                            ?>
                                
                                <td class="documento"><?=$img?></td>
                                <?php } ?>
                                </tr>
    <?php                   } ?>

                        </tbody>
                  <?php } ?>
                   <?php if($optSel == 2) { ?>
                        <thead>
                            <tr>
                                <th>NOME</th>
                                <th>EXAME ADMISSIONAL</th>
                                <th>FICHA DE CADASTRO CLT</th>
                                <th>CONTRATO DE TRABALHO</th>
                                <th>TV SORRINDO</th>
                                <th>INSCRIÇÃO NO PIS</th>
                                <th>CARTA DE REFERÊNCIA</th>
                                <th>SUSPENSÃO</th>
                                <th>ADVERTÊNCIA</th>
                                <th>AVISO PRÉVIO</th>
                                <th>DISPENSA</th>
                                <th>EXAME DEMISSIONAL</th>
                                <th>SOLICITAÇÃO DE VALE TRANSPORTE</th>
                                <th>DISPENSA DE VALE TRANSPORTE</th>
                                <th>SOLICITAÇÃO DO SALÁRIO FAMÍLIA</th>
                                <th>FICHA DE CADASTRO DO SALÁRIO FAMÍLIA</th>
                                <th>DEMISSÃO</th>
                                <th>CONTRATO DE EXPERIÊNCIA</th>
                            </tr>
                        </thead>      
                         <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome']; ?></td>
                                <?php $qr_docs_clt = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '2' ORDER BY id_doc ASC");
				       while($docs_clt = mysql_fetch_assoc($qr_docs_clt)) {
					   $qr_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '$docs_clt[id_doc]' AND id_clt = '{$row_rel['id']}'");  $verifica = mysql_num_rows($qr_verifica);
					   if((!empty($row_rel['pis'])) and $docs_clt['documento'] == "Inscrição no PIS") {
						 $img = '<div class="text-center success"><span class="fa fa-check"></span></div>';
					   } else {
						   if(!empty($verifica)) {
							$img = '<div class="text-center success"><span class="fa fa-check"></span></div>';
						   } else {
                                                        $img = '<div class="text-center danger"><span class="fa fa-times"></span></div>';
						   }
					   } ?> 
                                
                                
                 <td class="documento"><?=$img?></td>
                 <?php } ?>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                     <?php } ?>
                    
                    <?php if($optSel == 3) { ?>
                        <thead>
                            <tr>
                                <th>NOME</th>
                                <th>TV SORRINDO</th>
                                <th>FICHA DE ADESÃO</th>
                                <th>FICHA DE QUOTAS</th>
                                <th>FICHA DE CADASTRO</th>
                                <th>DESLIGAMENTO</th>
                                <th>PIS</th>
                                <th>DEVOLUÇÃO DE QUOTAS</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome']; ?></td>
                                <?php $qr_docs_cooperado = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '3' ORDER BY id_doc ASC");
				       while($docs_cooperado = mysql_fetch_assoc($qr_docs_cooperado)) {
					   $qr_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '$docs_cooperado[id_doc]' AND id_clt = '{$row_rel['id']}'");  $verifica = mysql_num_rows($qr_verifica);
					   if((!empty($row_rel['pis'])) and $docs_cooperado['documento'] == "PIS") {
						 $img = '<div class="text-center success"><span class="fa fa-check"></span></div>';
					   } else {
						   if(!empty($verifica)) {
							 $img = '<div class="text-center success"><span class="fa fa-check"></span></div>';
						   } else {
							 $img = '<div class="text-center danger"><span class="fa fa-times"></span></div>';
						   }
					   } ?> 
                                <td class="documento"><?=$img?></td>
                                <?php } ?>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                    <?php } ?>
                        <?php if($optSel == 4) { ?>
                        <thead>
                            <tr>
                                <th>NOME</th>
                                <th>TV SORRINDO</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome']; ?></td>
                                <?php $qr_docs_cooperado = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '4' ORDER BY id_doc ASC");
				       while($docs_cooperado = mysql_fetch_assoc($qr_docs_cooperado)) {
					   $qr_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '$docs_cooperado[id_doc]' AND id_clt = '{$row_rel['id']}'");  $verifica = mysql_num_rows($qr_verifica);
					   if((!empty($row_rel['pis'])) and $docs_cooperado['documento'] == "PIS") {
						 $img = '<div class="text-center success"><span class="fa fa-check"></span></div>';
					   } else {
						   if(!empty($verifica)) {
							 $img = '<div class="text-center success"><span class="fa fa-check"></span></div>';
						   } else {
							 $img = '<div class="text-center danger"><span class="fa fa-times"></span></div>';
						   }
					   } ?> 
                                <td class="documento"><?=$img?></td>
                                <?php } ?>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                    <?php } ?>
                    </table>
            <?php } ?>
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
                $(function () {
                    $(".bt-image").on("click", function () {
                        var id = $(this).data("id");
                        var contratacao = $(this).data("contratacao");
                        var nome = $(this).parents("tr").find("td:first").html();
                        thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
                    });
                });
                $(function () {
                    $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
                });
        </script>

    </body>
</html>
<!-- A -->