<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/SaidaClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$saida = new Saida();
$global = new GlobalClass();

if(isset($_REQUEST['filtrar'])){       
    $filtro = true;
    $result = $saida->getNF();
    $total_nfs = mysql_num_rows($result);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if(isset($_REQUEST['projeto'])){
    $regiaoR = $_REQUEST['regiao'];
    $projetoR = $_REQUEST['projeto'];    
    $mesR = $_REQUEST['mes'];
    $anoR = $_REQUEST['ano'];
}

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Relatório de Notas Fiscais");
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de Notas Fiscais</title>
        
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Relatório de Notas Fiscais</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">                                
                
                <div class="panel panel-default">
                    <div class="panel-heading">Relatório de Notas Fiscais</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Região</label>
                            <div class="col-lg-4">
                                <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='required[custom[select]] form-control'");  ?>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Projeto</label>
                            <div class="col-lg-4">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Competência</label>
                            <div class="col-lg-4">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <?php echo montaSelect(mesesArray(),$mesR, "id='mes' name='mes' class='required[custom[select]] form-control'"); ?>
                                    <span class="input-group-addon">Ano</span>
                                    <?php echo montaSelect(AnosArray(2008,null),$anoR, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="filtrar" id="filt" value="Gerar" class="btn btn-primary" />
                    </div>
                </div>
            </form>
            
            <?php
            if ($filtro) {
                if ($total_nfs > 0) {
            ?>
            
            <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle'>
                <thead>
                    <tr class="bg-primary">
                        <th>Emissão NF</th>
                        <th>Entrada da<br/>Mercadoria</th>
                        <th>Número NF</th>
                        <th>Descrição(Material/Serviço)</th>
                        <th>Nome do Fornecedor</th>
                        <th>Valor Bruto da NF</th>
                        <th>IRRF</th>
                        <th>ISS RF</th>
                        <th>PIS/COFINS/<br/>CSLL RF</th>                                    
                        <th>Valor Líquido da NF</th>
                        <th>Pago?</th>                        
                    </tr>
                </thead>
                <tbody>
                    
                    <?php 
                    while ($row = mysql_fetch_assoc($result)) { 
                        $bruto = str_replace(",", ".", $row['valor_bruto']);
                    ?>
                                   
                    <tr>
                        <td><?php echo $row['dt_emissao_nfbr']; ?></td>
                        <td><?php echo $row['']; ?></td>
                        <td><?php echo $row['n_documento']; ?></td>
                        <td><?php echo $row['assunto']; ?></td>
                        <td><?php echo $row['c_fantasia']; ?></td>
                        <td><?php echo formataMoeda($bruto); ?></td>
                        <td>R$ 0,00</td>
                        <td>R$ 0,00</td>
                        <td>R$ 0,00</td>                        
                        <td>R$ 0,00</td>
                        <td><?php echo ($row['status'] == 1) ? "Não" : "Sim"; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">                    
                    Nenhum registro encontrado
                </div>
            <?php }
            } ?>
            
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>       
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {                
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");
                
                $("#form1").validationEngine({promptPosition : "topRight"});                
            });
        </script>
    </body>
</html>
