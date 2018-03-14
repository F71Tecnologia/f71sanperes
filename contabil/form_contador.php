<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}
     
include("../conn.php");
include("../classes/global.php");
include("../classes/ProjetoClass.php");
include("../admin/prestadores/MunicipiosClass.php");
include("../classes/ContabilContadorClass.php");
include("../wfunction.php");

$objContador = new ContabilContadorClass();
$objMunicipio = new MunicipiosClass();
$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$global = new GlobalClass();

$breadcrumb_config = array("nivel" => "../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => "Contador", "id_form" => "form-cadastro");

$lista_contador = $objContador->listar();
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="../favicon.png">

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-compras.css" rel="stylesheet" type="text/css">

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-contabil-header">
                        <h2><span class="glyphicon glyphicon-briefcase"></span> - Contabilidade <small>- Contador</small></h2>
                    </div>
                    <form action="contador_controle.php" method="post" class="form-horizontal" id="form-cadastro" enctype="multipart/form-data">
                        <div class="text-right">
                            <button onclick="javascript: location.href='frm_cadastro_contador.php'" type="button" class="btn btn-info btn-xs" id="novo_contador" name="novo_contador" value="<?=$value['id_contador']?>" data-id="<?=$value['id_conta']?>" title="Novo" data-toggle="tooltip">
                                <span class="glyphicon glyphicon-plus"></span> Novo Contador
                            </button>
                        </div>
                        <br>
                        <fieldset>
                            <div class="panel panel-default">
                                <table class="table table-condensed table-striped">
                                    <thead class="text text-sm">
                                        <tr>
                                            <th>Nome</th>
                                            <th>CPF</th>
                                            <th>CRC</th>
                                            <th>Profissional</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                       <?php foreach ($lista_contador as $value) { 
                                           if($value['profissional'] == 1) {
                                            $profissional = 'TÉCNICO CONTABIL';
                                           } else {                                            
                                               $profissional = 'CONTADOR';
                                           } ?>
                                        <tr id="tr-<?= $value['id_contador'] ?>">
                                            <td><?= $value['nome'] ?> </td>
                                            <td><?= $value['cpf'] ?> </td>
                                            <td><?= $value['crc_uf']." ".$value['crc']." - ".$value['crc_control'] ?> </td>
                                            <td><?php echo $profissional ?></td>
                                            <td class="text text-right">
                                                <button type="button" class="btn btn-warning btn-xs" id="edita_contador" name="edita_contador" value="<?=$value['id_contador']?>" data-id="<?=$value['id_contador']?>" title="Editar" data-toggle="tooltip">
                                                    <span class="glyphicon glyphicon-edit"></span>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-xs" id="cancelar_contador" name="cancelar_contador" value="<?=$value['id_contador']?>" data-cancelar_id="<?=$value['id_contador']?>" data-nome="<?=$value['nome']?>" title="Desligar" data-toggle="tooltip">
                                                    <span class="glyphicon glyphicon-trash"></span>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>                
            <?php include_once '../template/footer.php'; ?>
        </div><!-- container -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery.form.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/form_contador.js" type="text/javascript"></script>
    </body>
</html>