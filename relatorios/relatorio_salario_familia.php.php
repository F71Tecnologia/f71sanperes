<?php

/**
 * Arquivo para geração de relatório de vale-transporte
 * 
 * @file      relatorio_vale_transporte.php
 * @license   
 * @link      
 * @copyright 2017 F71
 * @author    Juarez Garritano <juarez@f71.com.br>
 * @package   
 * @access    public  
 * @version: 2.00.L0000 - 20/03/2017 - Juarez - Versão Inicial: ALteração do Layou Antigo Para Bootstrap
 * 
 * 
 * @todo 
 * @example:  
 * 
 * @author 
 * 
 * @copyright www.f71.com.br
 */

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
$optRegiao = getRegioes();
$ACOES = new Acoes();

$opt = array("2"=>"CLT","1"=>"Autônomo","3"=>"Cooperado","4"=>"Autônomo/PJ");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $nome_anterior = "";

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    $contratacao = ($tipo_contratacao == "2")? "clt" : "autonomo";
    
    if($tipo_contratacao == 2) {
        $str_qr_relatorio = "SELECT A.nome, B.nome1, B.nome2, B.nome3,
                            B.nome4, B.nome5, B.nome6,
                            date_format(data1, '%d/%m/%Y') AS data1br, 
                            date_format(data2, '%d/%m/%Y') AS data2br, 
                            date_format(data3, '%d/%m/%Y') AS data3br, 
                            date_format(data4, '%d/%m/%Y') AS data4br, 
                            date_format(data5, '%d/%m/%Y') AS data5br,
                            date_format(data6, '%d/%m/%Y') AS data6br
                            FROM rh_clt AS A
                            LEFT JOIN dependentes AS B
                            ON B.id_bolsista = A.id_clt
                            WHERE A.id_regiao = '$id_regiao'
                            AND A.nome = B.nome
                            AND B.nome1 != '' ";
    }
    else {
        $str_qr_relatorio = "SELECT A.nome, B.nome1, B.nome2, B.nome3,
                            B.nome4, B.nome5, B.nome6,
                            date_format(data1, '%d/%m/%Y') AS data1br, 
                            date_format(data2, '%d/%m/%Y') AS data2br, 
                            date_format(data3, '%d/%m/%Y') AS data3br, 
                            date_format(data4, '%d/%m/%Y') AS data4br, 
                            date_format(data5, '%d/%m/%Y') AS data5br,
                            date_format(data6, '%d/%m/%Y') AS data6br
                            FROM autonomo AS A
                            LEFT JOIN dependentes AS B
                            ON B.id_bolsista = A.id_autonomo
                            WHERE A.id_regiao = '$id_regiao'
                            AND A.nome = B.nome
                            AND B.nome1 != '' 
                            AND A.tipo_contratacao = '$tipo_contratacao' ";
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
        
        <title>:: Intranet :: Relatório de Salário Família</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
         <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span>Relatório Salário Famíilia</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">
                <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                                
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-sm-4 control-label hidden-print">Região</label>
                            <div class="col-sm-5">
                               <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="select" class="col-sm-4 control-label hidden-print">Projeto</label>
                            <div class="col-sm-5">
                               <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="select" class="col-sm-4 control-label hidden-print">Projeto</label>
                            <div class="col-sm-5">
                               <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo', 'class' => 'form-control')); ?> <span class="loader"></span>
                            </div>
                        </div>
                        
                        
                       </div>
                    
                    <div class="panel-footer text-right hidden-print">
                        <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar Para Excel</button>
                        <?php } ?>
                        <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                            if($ACOES->verifica_permissoes(85)) { ?>
                            <button type="submit" name="todos_projetos" value="Gerar de Todos Projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Gerar Todos Os Projetos</button>
                        <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary" ><span class="fa fa-filter"></span> Gerar</button>
                    </div>
                </div>
            </form>
            
            <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                <table id="tbRelatorio" class="table table-striped table-condensed table-bordered table-hover text-sm valign-middle">
                    <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { ?>
                        <?php if($nome_anterior != $row_rel['nome']) { $cont = 0; ?>
                            <thead>
                                <tr>
                                    <th align="center" colspan="2" style="background-color: #E6E6E6;">
                                        <?php echo $row_rel['nome']; ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th>NOME DEPENDENTE</th>
                                    <th>DATA NASCIMENTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for($num_dependente = 1; !empty($row_rel['nome'.$num_dependente]); $num_dependente++) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                                <tr class="<?php echo $class ?>">
                                    <td><?php echo $row_rel['nome'.$num_dependente] ?></td>
                                    <td> <?php echo $row_rel['data'.$num_dependente.'br']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        <?php } ?>
                    <?php $nome_anterior = $row_rel['nome']; $num_dependente++; } ?>
                  
                </table>
            <?php 
            }
            ?>
            
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
                $(".bt-image").on("click", function() {
                    var id = $(this).data("id");
                    var contratacao = $(this).data("contratacao");
                    var nome = $(this).parents("tr").find("td:first").html();
                    thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
                });
            });
            $(function() {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
        </script>
    </body>
</html>
