<?php // session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/PrestadorServicoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$id_regiao = $usuario['id_regiao'];
$id_usuario = $usuario['id_funcionario'];
$id_master = $usuario['id_master'];

$prestador = new PrestadorServico();

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO PREENCHIDOS */
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date("Y");

$filtro = false;
$simples = false;

if(isset($_REQUEST['filtrar'])){
    $filtro = true;
    $result = $prestador->getPrestadorFinanceiro($id_master);
    $total_prestador = mysql_num_rows($result);
}elseif(isset($_REQUEST['simplificado'])){
    $simples = true;
    $result_simples = $prestador->getPrestadorFinanceiroSimplificado($id_master);
    $total_prestador_simples = mysql_num_rows($result_simples);
}

if($anual == 1){
    $periodo = "em ".date('Y');
}else{
    $periodo = "de {$dataIni} a {$dataFim}";
}

$ids_acesso = array('64','65','68','9','27','5','1','77','80','85','87');

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Relatório de Prestação de Serviços");
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Prestação de Serviços</title>

        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/datepicker.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Relatório de Prestação de Serviços</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                
                <?php if(isset($_SESSION['regiao'])){ ?>                
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <?php } ?>
                
                <div class="panel panel-default">
                    <div class="panel-heading">Relatório de Prestador</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Ano</label>
                            <div class="col-lg-4">
                                <?php echo montaSelect(AnosArray(null,null),$anoR, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary" />
                        <input type="submit" name="simplificado" id="simplificado" value="Simplificado" class="btn btn-primary" />
                    </div>
                </div>
            </form>
            
            <?php
            if ($filtro) {
                if ($total_prestador > 0) {
            ?>
            
            <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle'>
                <thead>
                    <tr class="bg-primary">
                        <th>Mês</th>
                        <th>Qtd de saídas</th>
                        <th>Valor</th>
                    </tr>
                    <?php while ($row = mysql_fetch_assoc($result)) { ?>
                    <tr class="warning">
                        <td colspan="7" class="text-center"><?php echo "{$row['c_cnpj']} - {$row['c_razao']}"; ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result_saida = $prestador->getSaidasPrestador($row['id_prestador']);
                    
                    while ($row_s = mysql_fetch_assoc($result_saida)) {
                    ?>
                    <tr>
                        <td><?php echo ($row_s['qntd'] == "") ? mesesArray($row_s['mes']) : "<a href='javascript:;' class='clk' data-key='".$row['id_prestador']."_".$row_s['mes']."'>".mesesArray($row_s['mes'])."</a>"; ?></td>
                        <td><?php echo ($row_s['qntd'] == "") ? "-" : $row_s['qntd']; ?></td>
                        <td><?php echo formataMoeda($row_s['valor']); ?></td>
                    </tr>
                    
                    <?php
                    if($row_s['qntd'] != ""){
                        $res = $prestador->getSaidasEspecifica($row['id_prestador'], $row_s['mes']);
                    ?>
                    <tr class="<?php echo $row['id_prestador']."_".$row_s['mes']; ?> occ active">
                        <td colspan="3">
                            <table class='table table-bordered table-hover'>
                                <tbody>
                                    <?php 
                                    while ($rowd = mysql_fetch_assoc($res)) { 
                                        $especificad = ($rowd['especifica'] == "") ? "-" : $rowd['especifica'];
                                        $darfd = ($rowd['darf'] == 1) ? "DARF" : "-";                                        
                                        
                                        if($rowd['estorno'] == 2){
                                            $valord = $rowd['cvalor']." - ".$rowd['valor_estorno_parcial'];
                                        }else{
                                            $valord = $rowd['cvalor'];
                                        }                                       
                                    ?>
                                    <tr>
                                        <td><?php echo $rowd['id_saida']; ?></td>
                                        <td><?php echo $rowd['nome']; ?></td>
                                        <td><?php echo $especificad; ?></td>
                                        <td><?php echo formataMoeda($valord); ?></td>
                                        <td><?php echo $rowd['vencimento']; ?></td>
                                        <td><?php echo $darfd; ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-xs btn-danger btn-outline arq" data-key="<?php echo $rowd['id_saida']; ?>"><span class='fa fa-paperclip'></span></a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
            
            <div class="clear"></div>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">
                    Nenhum registro encontrado
                </div>
            <?php }
            }elseif($simples){
                if ($total_prestador_simples > 0) {
            ?>
            
            <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle'>
                <thead>
                    <tr class="bg-primary">
                        <th>N° da Saída</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Data de Pagamento</th>
                        <th>DARF</th>
                    </tr>                    
                </thead>
                <tbody>
                    <?php 
                        $nome_prestador = "";
                        while ($row_simples = mysql_fetch_assoc($result_simples)) {
                            if($nome_prestador != $row_simples['c_razao']){
                                $nome_prestador = $row_simples['c_razao'];  ?>                      
                                <tr class="warning">
                                    <th colspan="6" class="text-center"><?php echo $nome_prestador; ?></th>
                                </tr>
                            <?php  } ?>    
                        <?php                     

                            $especifica = ($row_simples['bug'] == "") ? "-" : $row_simples['bug'];
                            $darf = ($row_simples['darf'] == 1) ? "<span class=\"text-success\">SIM</span>" : "<span class=\"text-danger\">NÃO</span>";

                            if($row_simples['estorno'] == 2){
                                $valor = $row_simples['cvalor']." - ".$row_simples['valor_estorno_parcial'];
                            }else{
                                $valor = $row_simples['cvalor'];
                            }
                        ?>
                        
                        <tr>
                            <td><?php echo $row_simples['id_saida']; ?></td>
                            <td><?php echo $row_simples['nome']; ?></td>
                            <!--isso foi feito, pois existem cadastrado sem espaços, ex.: numeros de nfs separados por virgula, porém sem espaço-->
                            <td><?php echo str_replace('/','/ ',preg_replace('/,/',', ',$especifica)); ?></td>
                            <td><?php echo formataMoeda($valor); ?></td>
                            <td><?php echo $row_simples['vencimento']; ?></td>
                            <td><?php echo $darf; ?></td>
                        </tr>
                    
                    <?php } ?>
                </tbody>
            </table>
            
            <div class="clear"></div>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">
                    Nenhum registro encontrado
                </div>
            <?php }} ?>
            
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/financeiro/prestador.js"></script>
        <script src="../../js/global.js"></script>        
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>        
    </body>
</html>