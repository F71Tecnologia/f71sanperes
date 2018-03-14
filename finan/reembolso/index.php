<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/ReembolsoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$reembolso = new Reembolso();

$result = $reembolso->getReembolso();
$total_reemb = mysql_num_rows($result);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if(isset($_REQUEST['regiao'])){
    $regiaoR = $_REQUEST['regiao'];
}elseif(isset($_SESSION['regiao'])){
    $regiaoR = $_SESSION['regiao'];
}elseif(isset($_SESSION['regiao_select'])) {
    $regiaoR = $_SESSION['regiao_select'];
}

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Reembolso");
//$breadcrumb_pages = $breadcrumb_pages_array[$caminho];
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Reembolso</title>

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
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Reembolso</small></h2></div>
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
                    <div class="panel-body text-right">
                        <input type="hidden" name="id" id="id" value="" />
                        <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?php echo $regiaoR; ?>" />
                        <input type="hidden" name="pausa" id="pausa" value="<?php echo $_SESSION['pausa']; ?>" />
                        <input type="hidden" name="volta" id="volta" value="<?php echo $_SESSION['regiao_select']; ?>" />                            
                        <button type="button" class="button btn btn-success" id="novoReembolso" name="novo"><span class="fa fa-plus-circle"></span>&nbsp;&nbsp;Solicitar Reembolso</button>
                    </div>
                </div>
            </form>
            
            <?php if ($total_reemb > 0) { ?>
            
            <table class='table table-hover table-striped table-bordered table-condensed text-sm valign-middle'>
                <thead>
                    <tr class="bg-primary">
                        <th>COD</th>
                        <th>Nome</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysql_fetch_assoc($result)) {
                        $nome = ($row['funcionario'] == 1) ? $row['nome_fun'] : $row['nome_ree'];
                        $codigo = sprintf("%05d",$row['id_reembolso']);
                        
                        if($row['status'] == 1){
                            $status = 'Aguardando aprovação';
                            $back_status = 'primary';
                            $print = "<img src='../../imagens/icones/icon-print.gif' title='Visualizar' class='bt-image' data-type='gerar_doc' data-key='{$row['id_reembolso']}' />";
                        }elseif($row['status'] == 2){
                            $status = 'Aprovado';
                            $back_status = 'success';
                        }else{
                            $status = 'Recusado';
                            $back_status = 'danger';
                            $cod = $codigo;
                        }
                    ?>
                    <tr id="<?php echo $row['id_reembolso']; ?>">
                        <td><?php echo $codigo; ?></td>
                        <td><?php echo acentoMaiusculo($nome); ?></td>
                        <td><?php echo formataMoeda($row['valor']); ?></td>
                        <td><?php echo $row['data']; ?></td>
                        <td><span class="label label-<?php echo $back_status; ?>"><?php echo $status; ?></span></td>
                        <td><?php echo $print; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">                    
                    Nenhum registro encontrado
                </div>
            <?php } ?>
            <?php include("../../template/footer.php"); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/financeiro/reembolso.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({ promptPosition : "topRight" });
            });
        </script>
    </body>
</html>