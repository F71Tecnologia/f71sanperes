<?php
        // session_start();
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
	
	$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"6", "area"=>"Sistema", "id_form"=>"form1", "ativo"=>"Gestão de Funcionários");
	$breadcrumb_pages = array();
	
	$ObjFunc = new FuncionarioClass();
        
	$oculto = (isset($_REQUEST['oculto'])) ? $_REQUEST['oculto'] : 0 ;
	$funcionarios = $ObjFunc->listFuncionariosAtivos($oculto);
	
	//USUÁRIOS QUE PODEM SIMULAR ACESSO
	//$arraySimula = array(9 => '', 87 => '', 158 => '', 179 => '', 202 => '', 255 => '', 256 => '', 257 => '', 258 => '', 259 => '', 260 => '',278 => '',355 => '');
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
            <form id="form-user" action="index.php" method="post"> 
                <div class="pull-right">
                                <?php if($usuario['oculto'] == 1) { ?>
                                    <?php if($oculto == 0) { ?>
                        <a href="#" class="btn btn-info" id="showHidden" oculto="1"><i class="fa fa-user"></i> Ver Adm</a>
                        <input type="hidden" value="1" name="oculto" id="oculto">
                                            <?php } else { ?>
                                                <a href="#" class="btn btn-info" id="showHidden" oculto="0"><i class="fa fa-user"></i> Ver Usuários</a>
                                                <input type="hidden" value="0" name="oculto" id="oculto">
                                            <?php } ?>
                                    <?php } ?>
                        <a href="usuarios_inativos.php" class="btn btn-danger"><i class="fa fa-search"></i> Ver Usuários Desativados</a>
                </div>
            
            
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
                    <?php foreach($funcionarios as $row) {
                                ?>
                                <tr>
                                <td><?php echo $row['id_funcionario']; ?></td>
                                <td><?php echo $row['nome']; ?></td>
                                <td><?php echo ($row['acesso_dias']==7) ? "<kbd>Todos os Dias</kbd>":"Durante a Semana"; ?></td>
                                <td><?php echo $row['horario_inicio'] . " até " . $row['horario_fim']; ?></td>
                                <td><?php echo $row['login']; ?></td>

                                <td>
                                    <?php if($usuario['oculto'] == 1 && $oculto == 0){?><a href="javascript:;" onclick="window.open('../../simula_user.php?funcionario=<?=$row['0']?>','','width=960,height=600,scrollbars=yes,resizable=yes')"><i class="btn btn-default btn-sm fa fa-eye" title="Simular Acesso"></i></a><?php } ?>
                                    <?php if($usuario['oculto'] == 1 && $oculto == 0){?><a href="javascript:;"><i class="btn btn-primary btn-sm fa fa-copy duplicar" data-key="<?= $row['id_funcionario'] ?>" title="Copiar Usuário"></i></a><?php } ?>
                                    <a href="../../cadastro2.php?id_cadastro=25&funcionario=<?=$row['id_funcionario']?>"><i class="btn btn-warning btn-sm fa fa-key" title="Alterar Senha"></i></a>
                                    <a href="form_usuario.php?user=<?=$row['id_funcionario']?>&pag=1"><i class="btn btn-success btn-sm fa fa-pencil" title="Editar"></i></a>
                                    <!--a href="javascript:void(0);" class="vin_clt" data-id="<?php echo $row['id_funcionario']; ?>"><i class=" btn btn-default btn-sm glyphicon glyphicon-paperclip" title="vincular"></i></a-->
                                    <a href="../../ver_tudo.php?id=20&funcionario=<?=$row['id_funcionario']?>"><i class="btn btn-info btn-sm fa fa-tasks" title="Ver Log"></i></a>
                                    <?php if($usuario['oculto'] == 1 && $oculto == 1) { ?><a type="button"><i  data-key="<?php echo $row['id_funcionario']; ?>" class="btn btn-inverse btn-sm fa fa-check-square-o conceder_permissao" title="Conceder Permissão"></i></a><?php } ?>
                                     <a href="../../cadastro2.php?id_cadastro=25&funcionario=<?=$row['id_funcionario']?>&excluir=1"><i class="btn btn-danger btn-sm fa fa-trash" title="Desativar Usuários"></i></a>
						
                                </td>
                                </tr>
                                <?php 
                                }
                        ?>
	</tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-center">Total de Usuários Ativos: <?php echo count($funcionarios); ?></th>
                        </tr>
                    </tfoot>
	</table>
                </form>
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
                
                $('body').on('click', '.duplicar', function () {
                    var id_funcionario = $(this).data('key');

                    var msg =
                            $('<form>', {action: 'duplicar_funcionario.php', method: 'post', id: 'form_duplicar', class: 'form-horizontal'}).append(
                            $('<div>', {class: 'panel panel-default'}).append(
                            $('<div>', {class: 'panel-body'}).append(
                            $('<div>', {class: 'form-group'}).append(
                            $('<div>', {class: 'col-xs-12'}).append(
                            $('<label>', {class: 'control-label', html: 'Nome'}),
                            $('<input>', {class: 'form-control validate[required]', type: 'text', name: 'nome'})
                            )
                            ),
                            $('<div>', {class: 'form-group'}).append(
                            $('<div>', {class: 'col-xs-12'}).append(
                            $('<label>', {class: 'control-label', html: 'Data Nascimento'}),
                            $('<input>', {class: 'data form-control validate[required]', type: 'text', name: 'data_nasc'})
                            )
                            ),
                            $('<div>', {class: 'form-group'}).append(
                            $('<div>', {class: 'col-xs-12'}).append(
                            $('<label>', {class: 'control-label', html: 'Login'}),
                            $('<input>', {class: 'form-control validate[required]', type: 'text', name: 'login'}),
                            )
                            ),
                            $('<div>', {class: 'form-group'}).append(
                            $('<div>', {class: 'col-xs-12'}).append(
                            $('<label>', {class: 'control-label', html: 'E-mail'}),
                            $('<input>', {class: 'form-control', type: 'text', name: 'email2'}),
                            $('<input>', {type: 'hidden', name: 'id_funcionario', value: id_funcionario}),
                            $('<input>', {type: 'hidden', name: 'action', value: 'duplicar_funcionario'})
                            )
                            ),
                            $('<div>', {class: 'clear'})
                            )
                            )
                            );
                    msg.find('.data').datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true
                    }).mask("99/99/9999");

                    new BootstrapDialog({
                        nl2br: false,
                        title: 'Dublicar Usuário',
                        message: msg,
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
                                    $("#form_duplicar").validationEngine();
                                    if ($("#form_duplicar").validationEngine('validate')) {
                                        $.post("duplicar_funcionario.php", $("#form_duplicar").serialize(), function (resultado) {
                                            if (resultado) {
                                                bootAlert('Funcionário Duplicado com Sucesso!', 'Funcionário Duplicado', function () {
                                                    location.reload();
                                                }, 'success');
                                            }
                                        });
                                    } else {
                                        return false;
                                    }
                                }
                            }]
                    }).open();
                });
            });
            $(document).ready(function () {
                $('#showHidden').on("click", function () {
                    $('#form-user').submit();
                });
            });


            $('body').on('click','.conceder_permissao',function(){
                var id_funcionario = $(this).data('key');

                bootConfirm('Deseja Conceder Todas as Permissões?', 'Confirmação', function(data){
                    if(data == true ){
                        $.post('exibir.php', {id_funcionario:id_funcionario},function(resultado){
                            bootAlert('Permissões Concedidas com Sucesso!', 'Permissões Concedidas!',function(){ location.reload(); },'success');
                        });
                    }
                }, 'success');
            });
        </script>
    </body>
</html>