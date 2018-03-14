<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/PagamentoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$pagamento = new Pagamento();

$id_regiao = $usuario['id_regiao'];

$result = $pagamento->getPagamento($id_regiao);
$total_pgt = mysql_num_rows($result);

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Gestão de Tipos de Pagamentos");
//$breadcrumb_pages = $breadcrumb_pages_array[$caminho];
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Tipos de Pagamentos</title>

        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Gestão de Tipos de Pagamentos</small></h2></div>
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
                        <input type="hidden" name="pagamento" id="pagamento" value="" />
                        <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?php echo $id_regiao; ?>" />
                        <input type="hidden" name="pausa" id="pausa" value="<?php echo $_SESSION['pausa']; ?>" />
                        <input type="hidden" name="volta" id="volta" value="<?php echo $_SESSION['regiao_select']; ?>" />                            
                        <input type="submit" class="button btn btn-success" value="Novo Pagamento" name="novo" id="novoPgt" />                            
                    </div>
                </div>
            </form>
            
            <?php if ($total_pgt > 0) { ?>
            
            <table class="table table-bordered table-condensed table-hover text-sm valign-middle">
                <thead>
                    <tr class="bg-primary">
                        <th class="text-center">#</th>
                        <th>Tipo</th>
                        <!--<th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>-->
                        <th class="text-center" colspan="2">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php    
                    $sel_projeto = getProjetosRegiao($id_regiao);

                    while($row_proj = mysql_fetch_assoc($sel_projeto)){

                    $result_proj = $pagamento->getPagamentoProj($id_regiao, $row_proj['id_projeto']);

                    $projeto = "";
                    while ($row = mysql_fetch_assoc($result_proj)) {

                        if($projeto != $row_proj['nome']){
                            $projeto = $row_proj['nome'];
                            echo "<tr class='active text-center'><td colspan='4'>".ucwords($row_proj['nome'])."</td><tr />";
                        }
                    ?>
                    
                    <tr id="<?php echo $row['id_tipopg']; ?>">
                        <td class="text-center" width="70px"><?php echo acentoMaiusculo($row['id_tipopg']); ?></td>
                        <td><?php echo acentoMaiusculo($row['tipopg']); ?></td>
                        <!--<td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>-->
                        <td class="text-right" width="35px"><a heref="javascript:;" title="Editar" class="btn btn-xs btn-warning bt-image" data-type="editar" data-key="<?php echo $row['id_tipopg']; ?>"><i class="fa fa-edit"></i></a></td>
                        <td class="text-right" width="35px"><a heref="javascript:;" title="Excluir" class="btn btn-xs btn-danger bt-image" data-type="excluir" data-key="<?php echo $row['id_tipopg']; ?>"><i class="fa fa-trash-o"></i></a></td>
                    </tr>
                    <?php }} ?>
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
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/financeiro/pagamento.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({promptPosition : "topRight"});                
            });
        </script>
    </body>
</html>