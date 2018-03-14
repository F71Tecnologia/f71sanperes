<?php // session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/global.php");
include("../../classes/FuncionarioClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$global = new GlobalClass();
$ObjFunc = new FuncionarioClass();

if($_REQUEST['method'] == 'ativar_user'){
    $ObjFunc->setIdFuncionario($_REQUEST['id_funcionario']);
    $ObjFunc->getFuncionarioById();
    $ObjFunc->getRow();
    $ObjFunc->setStatusReg(1);
    
    $ObjFunc->update();
    echo $ObjFunc->getIdFuncionario();
    exit;
}

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"6", "area"=>"Sistema", "id_form"=>"form1", "ativo"=>"Gestão de Funcionários");
$breadcrumb_pages = array();

$funcionarios = $ObjFunc->listFuncionariosInativos();

//USUÁRIOS QUE PODEM SIMULAR ACESSO
$arraySimula = array(9 => '', 87 => '', 158 => '', 179 => '', 202 => '', 255 => '', 256 => '', 257 => '', 258 => '', 259 => '', 260 => '',278 => '');
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão de Usuários</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-sistema-header"><h2><span class="glyphicon glyphicon-phone"></span> - Sistema<small> - Gestão de Usuários</small></h2></div>
            
            <table class='table table-hover table-striped text-sm valign-middle'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Dias Acesso</th>
                        <th>Horario Acesso</th>
                        <th>Login</th>
                        <!--th>Descrição</th>
                        <th>Cadastrada por</th>
                        <th>Confirmada por</th>
                        <th>Valor Adicional</th-->
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($funcionarios as $row) { ?>
                    <tr>
                        <td><?php echo $row['id_funcionario']; ?></td>
                        <td><?php echo $row['nome']; ?></td>
                        <td><?php echo ($row['acesso_dias']==7) ? "<kbd>Todos os Dias</kbd>":"Durante a Semana"; ?></td>
                        <td><?php echo $row['horario_inicio'] . " até " . $row['horario_fim']; ?></td>
                        <td><?php echo $row['login']; ?></td>
                        
                        <td>
                            <button type="button" class="btn btn-primary btn-sm ativar" data-key="<?= $row['id_funcionario'] ?>"><i class="fa fa-arrow-circle-up" title="Ativar Usuários"></i></button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-center">Total de Usuários Ativos: <?php echo count($funcionarios); ?></th>
                    </tr>
                </tfoot>
            </table>
            
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {                
                $("#form1").validationEngine({promptPosition : "topRight"});
                
                //datepicker
                var options = new Array();
                options['language'] = 'pt-BR';
                $('.datepicker').datepicker(options);
                
                //janela modal.
                $('body').on('click', ".vin_clt", function () {
                    var id = $(this).data("id");
                    $.post('vinculo_clt.php', {id: id}, function (data) {
                        bootDialog(data);
                    });
                });
                
                //carrega projetos
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function (data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");


                $('#projeto').ajaxGetJson("../methods.php", {method: "carregaUnidades", projeto: $("#projeto").val(), regiao: $("#regiao").val()}, null, "unidade");
                
                $('body').on('click', '.ativar', function(){
                    var id_funcionario = $(this).data('key');
                    
                    new BootstrapDialog({
                        nl2br: false,
                        title: 'Ativar Usuário',
                        message: "Confirmar ativação do usuario?",
                        closable: false,
                        type: 'type-info',
                        buttons: [{
                                label: 'Cancelar',
                                action: function (dialog) {
//                                    typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
                                    dialog.close();
                                }
                            }, {
                                label: 'OK',
                                cssClass: 'btn-info',
                                action: function (dialog) {
                                    $.post("", { method: 'ativar_user', id_funcionario:id_funcionario }, function(resultado){
                                        if(resultado){
                                            bootAlert('Funcionário ativado com Sucesso!','Funcionário Ativado',function(){ location.reload(); },'success');
                                        }
                                    });
                                    
                                }
                            }]
                    }).open();
                });
                
            });
        </script>
    </body>
</html>