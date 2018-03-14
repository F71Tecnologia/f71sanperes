<?php

/**
 * Arquivo para geração de relatório de vale-transporte
 * 
 * @file      relatorio_vale_transporte.php
 * @license   
 * @link      
 * @copyright 2016 F71
 * @author    Jacques <jacques@f71.com.br>
 * @package   
 * @access    public  
 * @version: 3.00.L0000 - ??/??/???? - Não Definido - Versão Inicial 
 * @version: 3.00.L0020 - 23/11/2016 - Jacques      - Restauração o arquivo para o frontend (produção) pois o arquivo sumiu
 * @version: 3.00.L0021 - 23/11/2016 - Jacques      - Alguém colocou o código if(!empty($_REQUEST['gerar'])) antes do botão gerar que fez o botão sumir.  
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

include("../conn.php");
include("../classes/regiao.php");
include("../classes/projeto.php");
include("../classes/funcionario.php");
include("../classes_permissoes/regioes.class.php");
include("../classes_permissoes/acoes.class.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();


$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de Quotas Pagas</title>
        
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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span>Usuário de Vale Transporte</h2></div>
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
                       </div>
                    
                    <div class="panel-footer text-right hidden-print">
                        <?php if(!empty($_REQUEST['gerar'])){ ?>
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar Para Excel</button>
                        <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary" ><span class="fa fa-filter"></span> Gerar</button>
                        
                    </div>
                </div>
            </form> 
            
            <?php if(!empty($_REQUEST['gerar'])){ ?>
            <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-condensed table-bordered table-hover text-sm valign-middle" width="100%" style="page-break-after:auto;">
                <thead>
                    <tr>
                       <th>Projeto</th> 
                       <th>Nome</th> 
                       <th>Admissão</th> 
                       <th>Função</th>
                       <th>Status</th>
                       <th>Retorno</th>
                       <th>Salário</th>
                       <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //MOSTRA NOME E STATUS  
                    $sQuery = "
                            SELECT 
                                p.nome AS projeto,
                                c.id_clt,
                                c.nome,
                                DATE_FORMAT(c.data_entrada,'%d/%m/%Y') AS admissao,
                                st.especifica AS status,
                                c.transporte,
                                cs.nome AS funcao, 
                                cs.valor,
                                c.id_clt,
                                c.nome,
                                CASE c.status
                                     WHEN 10 THEN ''
                                     WHEN 40 THEN f.ferias_retorno
                                     ELSE e.evento_retorno
                                END data_retorno    
                            FROM rh_clt c  INNER JOIN curso cs ON c.id_curso = cs.id_curso
                                           INNER JOIN projeto p ON c.id_projeto = p.id_projeto
                                           INNER JOIN rhstatus st ON c.status = st.codigo
                                           LEFT JOIN (
                                                        SELECT id_clt,cod_status,DATE_FORMAT(data_retorno,'%d/%m/%Y') evento_retorno
                                                        FROM rh_eventos
                                                        WHERE DATE_FORMAT(data_retorno,'%Y%m%d') >= DATE_FORMAT(CURRENT_TIMESTAMP,'%Y%m%d') OR DATE_FORMAT(data_retorno,'%Y%m%d')=DATE_FORMAT('00000000','%Y%m%d') 
                                                        GROUP BY id_clt
                                                      ) e ON c.id_clt = e.id_clt AND c.status=e.cod_status
                                            LEFT JOIN (
                                                        SELECT id_clt,DATE_FORMAT(MAX(data_retorno),'%d/%m/%Y') ferias_retorno
                                                        FROM rh_ferias
                                                        GROUP BY id_clt
                                                       ) f ON c.id_clt = f.id_clt 	
                            WHERE c.transporte = '1' AND c.id_projeto = '$projetoSel' AND c.status < 60
                            ORDER BY c.nome                            
                            ";
                    
                    $result_vale = mysql_query($sQuery);
                    
                    while ($linha = mysql_fetch_assoc($result_vale)) {
                        echo "
                        <tr>
                            <td>{$linha['projeto']}</td>
                            <td>{$linha['nome']}</td>
                            <td>{$linha['admissao']}</td>                                
                            <td>{$linha['funcao']}</td>
                            <td>{$linha['status']}</td>                                
                            <td>{$linha['data_retorno']}</td>                                
                            <td>".number_format($linha['valor'],2,',','.')."</td>
                            <td>";

                                //TROCA O STATUS DE 1 PARA ATIDO  
                                if ($linha["transporte"] == "1") { echo "Ativo"; }

                        echo "
                            </td>
                            <!--<td>
                                <input type='checkbox' name='status[]' id='status_{$linha["id_clt"]}' value='{$linha["id_clt"]}' />
                                <label for='status_{$linha["id_clt"]}'>Desativar</label>
                            </td>-->
                        </tr>";
                    }
                    ?>
                </tbody>
                <!--<tr colspan='3'>
                    <td></td>
                    <td></td>
                    <td>
                    <input type='hidden' name='reg' value='{$regiao}' />
                    <input type='hidden' name='pro' value='{$projeto}' />    
                    <input type='submit' name='salvar' value='Salvar' style='float: right; padding:5px 8px; margin-right:25px; margin-bottom:10px;' /></td>
                </tr>-->
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
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function(data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");
            });
            
        </script>
    </body>
</html>
