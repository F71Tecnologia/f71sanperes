<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

if(isset($_REQUEST['filtrar'])){
    $id_regiao = $_REQUEST['regiao'];
    $filtro = true;
    $result = getBanco($_REQUEST['regiao']);
    $total_banco = mysql_num_rows($result);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if(isset($_REQUEST['regiao'])){    
    $regiaoR = $_REQUEST['regiao'];
}elseif(isset($_SESSION['regiao'])){    
    $regiaoR = $_SESSION['regiao'];
}elseif(isset($_SESSION['regiao_select'])) {    
    $regiaoR = $_SESSION['regiao_select'];
}

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Gestão de Bancos");
//$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Bancos</title>

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
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Gestão de Bancos</small></h2></div>
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
                    <div class="panel-body">
                        <label for="select" class="col-sm-2 control-label">Região</label>
                        <div class="col-sm-9">
                            <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='required[custom[select]] form-control'") ?>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                        <input type="hidden" name="banco" id="banco" value="" />
                        <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?php echo $regiaoR; ?>" />
                        <input type="hidden" name="pausa" id="pausa" value="<?php echo $_SESSION['pausa']; ?>" />
                        <input type="hidden" name="volta" id="volta" value="<?php echo $_SESSION['regiao_select']; ?>" />
                        <?php if ($filtro) { ?>
                        <button type="submit" class="button btn btn-success" value="Novo Banco" name="novo" id="novoBanco" ><i class="fa fa-plus"></i> Novo Banco</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
            
            <?php
            if ($filtro) {
                if ($total_banco > 0) { ?>
            
            <table class="table table-hover table-bordered table-condensed text-sm valign-middle">
                <thead>
                    <tr class="bg-primary">
                        <th></th>
                        <th class="text-center">#</th>
                        <th>Banco</th>
                        <th>Agência</th>
                        <th>Conta</th>
                        <th>Endereço</th>
                        <th>Telefone</th>
                        <th>Gerente</th>
                        <th class="text-center" colspan="2">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php    
                    $sel_projeto = getProjetosRegiao($id_regiao);

                    while($row_proj = mysql_fetch_assoc($sel_projeto)){

                    $result_proj = getBancoProj($id_regiao, $row_proj['id_projeto']);

                    $projeto = "";
                    while ($row = mysql_fetch_assoc($result_proj)) {

                        if($projeto != $row_proj['nome']){
                            $projeto = $row_proj['nome'];
                            echo "<tr class='active text-center'><td colspan='10'>".ucwords($row_proj['nome'])."</td><tr />";
                        }
                    ?>
                                        
                    <tr id="<?php echo $row['id_banco']; ?>">
                        <td class="td_img text-center"><img src='../../imagens/bancos/<?php echo $row['id_nacional']; ?>.jpg' width='25' height='25' align='absmiddle'></td>
                        <td class="text-center"><?php echo acentoMaiusculo($row['id_banco']); ?></td>
                        <td><?php echo acentoMaiusculo($row['nome']); ?></td>
                        <td><?php echo $row['agencia']; ?></td>
                        <td><?php echo $row['conta']; ?></td>
                        <td><?php echo acentoMaiusculo($row['endereco']); ?></td>
                        <td><?php echo $row['tel']; ?></td>
                        <td><?php echo acentoMaiusculo($row['gerente']); ?></td>
                        <td class="text-center"><a heref="javascript:;" title="Visualizar" class="btn btn-xs btn-primary bt-image" data-type="visualizar" data-key="<?=$row['id_banco']?>"><i class="fa fa-search"></i></a></td>
                        <td class="text-center"><a heref="javascript:;" title="Editar" class="btn btn-xs btn-warning bt-image" data-type="editar" data-key="<?=$row['id_banco']?>"><i class="fa fa-edit"></i></a></td>
                    </tr>
                    <?php }} ?>
                </tbody>
            </table>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">                    
                    Nenhum registro encontrado
                </div>
            <?php }
            } ?>
            
            <?php include("../../template/footer.php"); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/financeiro/banco.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({promptPosition : "topRight"});                
            });
        </script>
    </body>
</html>