<?php
include_once("../../conn.php");
include_once("../../wfunction.php");
include_once("../../classes/PermissoesClass.php");
include_once("../../classes/global.php");
include_once("permissoes_usuario.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$id_user = 204;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Sistema</title>

        <link rel="shortcut icon" href="favicon.ico" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container height_min">
            <form name="form" id="form" action="" method="post">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="page-header box-sistema-header"><h2><span class="fa fa-users"></span> - PERMISSÕES DE FUCIONÁRIOS</h2></div>
                        <div class="bs-component">
                            <ul class="nav nav-tabs" style="margin-bottom: 15px;">
                                <li class="active tab-master"><a href="#master" data-toggle="tab">MASTER</a></li>
                                <li class="disabled disabledTab tab-regiao"><a href="#regioes" data-toggle="tab">REGIÕES</a></li>
                                <li class="disabled disabledTab tab-acoes-botoes"><a href="#acoes_botoes" data-toggle="tab">AÇÕES/BOTÕES</a></li>
                            </ul>
                            <div id="myTabContent" class="tab-content">
                                <div class="tab-pane fade active in" id="master">
                                    <div class="input-group float_label_todos">
                                        <span class="input-group-addon">
                                            <input type="checkbox" id="todos_master" name="todos">
                                        </span>
                                        <label for="todos_master" class="label-group borda_suporte_right">Todos</label>
                                    </div>
                                    <ul class="checkbox-list">
                                        <?php foreach ($master as $key => $value) { ?>
                                            <li class="checkbox-lis-item">
                                                <?php $checked = (in_array($key, $permissao_master)) ? "checked" : ""; ?>
                                                <input type="checkbox" class="checkbox_master" name="master[]" id="master_<?= $key; ?>" value="<?php echo $key; ?>" <?php echo $checked; ?> /><label for="master_<?= $key; ?>"><?php echo $value; ?></label>
                                            </li>    
                                        <?php } ?>  
                                    </ul>
                                </div>
                                <div class="tab-pane fade in" id="regioes">regioes</div>
                                <div class="tab-pane fade in" id="acoes_botoes">açoes</div> 
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-footer text-right">                            
                    <button type="button" class="btn btn-default" id="volta_index" name="voltar"><span class="fa fa-reply"></span>Voltar</button>
                    <input type="button" class="btn btn-primary" name="cad_master" id="cad_master" value="Próximo" />
                    <input type="hidden" name="id_user" id="id_user" value="<?php echo $id_user; ?>" />
                </div>
            </form>
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
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $("#todos_master").click(function() {
                    if ($(this).prop("checked")) {
                        $(".checkbox_master").each(function() {
                            this.checked = true;
                        });
                    } else {
                        $(".checkbox_master").each(function() {
                            this.checked = false;
                        });
                    }
                });

                $(".nav-tabs a[data-toggle=tab]").on("click", function(e) {
                    if ($(this).parent().hasClass("disabled")) {
                        e.preventDefault();
                        return false;
                    }
                });

                $("#cad_master").click(function() {
                    var id_usuario = $("#id_user").val();
                    var master = $("#form").serialize();
                    if (master.length > 0) {
                        $.ajax({
                            url: "controller_permissao.php?method=cadastra_master&" + master,
                            type: "post",
                            dataType: "json",
                            success: function(data) {
                                if (data.status) {
                                    $(".nav-tabs a[href='#regioes']").tab('show');
                                    $(".tab-master").removeClass("active");
                                    $(".tab-regiao").removeClass("disabled disabledTab").addClass("active");
                                    if ($(".tab-regiao").hasClass("active")) {
                                        $.ajax({
                                            url: "controller_permissao.php",
                                            type: "post",
                                            dataType: "html",
                                            async: false,
                                            data:{
                                                method:"carregaRegiao",
                                                master:master
                                            },
                                            success: function(data){
                                                
                                            }
                                        });
                                    }
                                }
                            }
                        });
                    }
                });


            });
        </script>
    </body>
</html>






