<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/EmpresaClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$filtro = false;

if(validate($_REQUEST['filtrar'])){
    $filtro = true;
    $rsempresas = getEmpresa(validatePost('regiao',"INT"));
    $totalEmp = mysql_num_rows($rsempresas);
}


//SELECTBOX CARREGADO
$projetoR = isset($_REQUEST['regiao']) ? $_REQUEST['regiao'] : "";
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de RH - Empresa</title>

        <link rel="shortcut icon" href="favicon.ico" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                <fieldset>
                    <legend>Gestão de Empresas</legend>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Região</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $projetoR, "id='regiao' name='regiao' class='validate[required,custom[select]] form-control'"); ?>
                        </div>
                        <div class="col-lg-4">
                            <input type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary">
                        </div>
                    </div>
                </fieldset>
                
                <?php
                if ($filtro) {
                    if ($totalEmp > 0) {
                ?>
                <table class='table table-hover table-striped'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Empresa</th>
                            <th>CNPJ</th>
                            <th>Responsável</th>
                            <th colspan="3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysql_fetch_assoc($rsempresas)) { ?>
                            <tr>
                                <td><?php echo $row['id_empresa'];?></td>
                                <td><?php echo $row['nome'];?></td>
                                <td><?php echo $row['cnpj'];?></td>
                                <td><?php echo $row['responsavel'];?></td>
                                <td class="center">
                                    <img src="../../imagens/icones/icon-docview.gif" title="Visualizar" class="bt-image" data-type="ver" data-key="<?php echo $row['id_empresa']; ?>" />
                                </td>
                                <td class="center">
                                    <img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php echo $row['id_empresa']; ?>" />
                                </td>
                                <td class="center">
                                    <img src="../../imagens/icones/icon-excluir.png" title="Excluir" class="bt-image" data-type="excluir" data-key="<?php echo $row['id_empresa']; ?>" />
                                </td>
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
                
                <a href='javascript:;' type="button" class="btn btn-default" id="volta_index" data-nivel='1' name="voltar"><span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar</a>
                <!--<button type="button" class="btn btn-default" id="volta_index" name="voltar"><span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar</button>-->
            </form>
            <div class="clear"></div>
            <footer>
                <div class="row">
                    <div class="page-header"></div>
                    <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                    <div class="col-lg-12">
                        <p>Pay All Fast 3.0</p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </div>
            </footer>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/tooltirces/js/bootstrap.min.jp.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $("#form1").validationEngine();
                
                $(".bt-image").click(function(){
                    var bt = $(this);
                    var type = bt.data('type');
                    console.log(type);
                });
                
                
            });
        </script>
    </body>
</html>