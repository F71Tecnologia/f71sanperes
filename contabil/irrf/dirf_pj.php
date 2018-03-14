<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/EventoClass.php");
include("../../classes/DirfPJClass.php");
$filtro = false;
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"38", "area"=>"Contabilidade", "ativo"=>"DIPJ", "id_form"=>"form1");

$rowMaster = montaQueryFirst("master", "cnpj,razao,nome", "id_master = {$usuario['id_master']}");

if(isset($_REQUEST['filtrar'])){
    $filtro = true;
    $anoCalen = $_REQUEST['ano'];
    $anoBase = $anoCalen - 1;
    
    $objDirf = new DirfPJ();
    $dados = $objDirf->getDados($usuario['id_master'], $anoBase);
    
    $num_rows = mysql_num_rows($dados);
    $empresa = "";
}

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Administrativo</title>

        <link rel="shortcut icon" href="../../favicon.png">

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-contabil-header"><h2><?php echo $icon['38'] ?> - CONTABILIDADE</h2></div>
                    <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                        <input type="hidden" name="home" id="home" value="" />

                        <h3>DIRF PJ</h3>

                        <fieldset>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="regiao" class="col-lg-2 col-md-2 control-label">CNPJ Matriz</label>
                                            <div class="col-lg-9 col-md-9">
                                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                                    <input type="text" id="master" name="master" value="<?php echo $rowMaster['razao']." - ".$rowMaster['cnpj']?> " class="form-control" disabled="" />
                                                    <span class="input-group-addon">Ano</span>
                                                    <?php echo montaSelect(anosArray(null,null,array("-1"=>"« Selecione »")), date('Y')+1, "id='ano' name='ano' class='form-control validate[required,custom[select]]'");?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <div class="row">
                                        <div class="col-lg-11 text-right">
                                            <button type="submit" name="filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        
                        
                        <?php if($filtro){?>
                            <?php if($num_rows > 0){?>
                        <div class="alert alert-info">
                            <strong>Ano calendário:</strong> <?php echo $anoCalen ?> - <strong>Ano Base:</strong> <?php echo $anoBase ?>
                        </div>
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Unidade</th>
                                    <!--th>Razão</th-->
                                    <th>CNPJ</th>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                    <th>Data Pagamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                while($row = mysql_fetch_assoc($dados)){
                                    if($empresa!=$row['c_razao']){
                                        echo "<tr><td colspan='6' class='info text-center'><strong>{$row['c_razao']}</strong></td></tr>";
                                        $empresa = $row['c_razao'];
                                    }
                                    
                                    ?>
                                <tr>
                                    <td><?php echo $row['id_saida'] ?></td>
                                    <td><?php echo $row['nome'] ?></td>
                                    <!--td><?php echo $row['c_razao'] ?></td-->
                                    <td><?php echo $row['c_cnpj'] ?></td>
                                    <td><?php echo $row['especifica'] ?></td>
                                    <td><?php echo $row['valor'] ?></td>
                                    <td><?php echo $row['data_pgBR'] ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                            <?php }else{ ?>
                                <div class="alert alert-danger">
                                    <strong>Ops!</strong> Nenhuma informação encontrada.
                                </div>
                            <?php }?>
                        
                        <?php }?>
                        
                    </form>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                
            });
        </script>
    </body>
</html>