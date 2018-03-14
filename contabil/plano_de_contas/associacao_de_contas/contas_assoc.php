<?php
    if (empty($_COOKIE['logado'])) {
        print "<script>location.href = '../../../login.php?entre=true';</script>";
    }
 
    include("../../../conn.php");
    include("../../../funcoes.php");
    include("../../../wfunction.php");
    include("../../../classes_permissoes/acoes.class.php");
    include("classes/AssocContasClass.php");
    
    $usuario = carregaUsuario();
    $objAcao = new Acoes();
    $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEï¿½ALHO (TROCA DE MASTER E DE REGIï¿½ES)

    $objContas = new AssocContasClass(); 

    $breadcrumb_config = array("nivel"=>"../../../", "key_btn"=>"38", "area"=>"Contabilidade", "id_form"=>"form1", "ativo"=>"Associaï¿½ï¿½o de Contas");
    //$breadcrumb_pages = array("Lista Projetos" => "ver.php");
?>
        <!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Associação de Contas</title>
        <link href="../../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <style> 
            .tamanho { width: 170px; white-space:normal; }
            .fontpeq { font-size: 14px; }
        </style>        
        <?php include("../../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="col-sm-12">
                <div class="page-header box-contabil-header"><h2><span class="glyphicon glyphicon-usd"></span> - CONTABILIDADE<small> - Associação de Contas (Financeiro X Contábil)</small></h2></div>
                <form action="contas_assoc_controle.php" method="post" class="form-horizontal" id="form_despesareceita">
                    <div class="panel panel-default">                        
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="projeto" class="col-sm-2 text-sm control-label">Projeto</label>
                                <div class="col-sm-6"><?= montaSelect(getProjetos($usuario['id_regiao']), $projetos, "id='projeto' name='projeto' class='form-control input-sm validate[required,custom[select]]'") ?></div>
                                <button type="submit" id="filtra" name="filtra" value="Filtrar" class="btn btn-default btn-sm"><i class="fa fa-check"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="contas"></div>
                </form>
                <div><?php include('../../../template/footer.php'); ?></div>
            </div>
        </div>
        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../../resources/dropzone/dropzone.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../js/global.js"></script>
        <script src="../../../js/jquery.form.js" type="text/javascript"></script>
        <script src="js/form_associacao.js" type="text/javascript"></script>
        <script>
        $(function(){
            $('#form_despesareceita').ajaxForm({
                success: 
                function(exibe){
                    $(".contas").html(exibe);
                }
            });
            
            $('body').on('click', '.deletar', function(){
                var tipo_assoc = $(this).data('tipo');
                var id_assoc = $(this).data('assoc');
                bootConfirm('Deseja excluir esta associação?','Confirmação de Exclusão',
                    function(data){
                        if(data == true){
                            $.post("", {bugger:Math.random(), action:'deletar', id_assoc:id_assoc, tipo:tipo_assoc }, function(resultado){
                                //console.log(resultado); return false;
                                if(!resultado){
                                    if(tipo_assoc == 'banco') {
                                        recarrega_banco();
                                    } else if(tipo_assoc == 'folha') {
                                        recarrega_folha();
                                    } else if(tipo_assoc == 'tipo') {
                                        recarrega_tipo();
                                    }
                                } else {
                                    console.log(resultado);
                                }
                            });
                        } 
                    },
                'danger');
            });
            
        });
        </script>
    </body>
</html>