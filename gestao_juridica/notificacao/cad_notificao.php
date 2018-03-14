<?php 
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
//include "../funcoes.php";
include "../include/criptografia.php";
include("../../classes_permissoes/regioes.class.php");
include("../../wfunction.php");

$obj_regiao = new Regioes();

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$id_user   = $_COOKIE['logado'];
$regiao    = $_GET['regiao'];
$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master FROM funcionario WHERE id_funcionario = '$id_user'");
$row_funcionario   = mysql_fetch_array($query_funcionario);
$tipo_user         = $row_funcionario['tipo_usuario'];

$query_master      = mysql_query("SELECT id_master FROM regioes WHERE id_master = '$row_funcionario[id_master];'");
$id_master         = @mysql_result($query_master,0);

$regioes = new Regioes();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o  Jur&iacute;dica</title> 

<link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all"/>
<link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all"/>
<link href="../../resources/css/main.css" rel="stylesheet" media="screen"/>
<link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen"/>
<link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
<link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
<link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen"/>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>

<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
<script src="../../jquery/jquery.tools.min.js" type="text/javascript"></script>
<script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
	$('#data_limite').mask('99/99/9999');
	
	$('#regiao').change(function(){
		
		var regiao = $(this).val();
		$.ajax({
			url:'../action.preenche_select.php?regiao='+regiao,
			success: function(resposta) {			
				$('#projeto').html( '<option value="todos">TODOS</option>' + resposta);				
				}		
			});
		});
		



$('.add_documento').click(function(){	
	var campo = '<div> <input type="text" class="form-control" name="documentos[]"  /> <a href="#" onclick="$(this).parent().remove(); return false;"><img src="../../imagens/excluir.png" width="18" height="18" title="Excluuir"/> </a> </div>';	
	$('#documentos').prepend(campo);
	});
	
$('.add_responsavel').click(function(){
	
	
		$.ajax({
			url: '../action.preenche_select.php?funcionario',
			success: function(resposta) {
			
					
			var campo = '<div> <select class="form-control" name="responsaveis[]">  <option value=""> Selecione o responsável...</option> '+resposta+'</select> <a href="#" onclick="$(this).parent().remove(); return false;"><img src="../../imagens/excluir.png" width="18" height="18" title="Excluir"/> </a> </div>';	
			
			$('#responsaveis').prepend(campo);
			}
	
	 });
});
	



	
$('.add_documento' ).trigger('click');	
$('.add_responsavel' ).trigger('click');	
});	

	


</script>

<style>
.add_documento,.add_responsavel{
cursor:pointer;	
}
</style>

<!--<link rel="stylesheet" type="text/css" href="../../adm/css/estrutura.css"/>-->
</head>
<body  class="fundo_juridico" >
     <?php include("../../template/navbar_default.php"); ?>
    <div class="container"> 
        <div class="page-header box-juridico-header"><h2><span class="glyphicon glyphicon-briefcase"></span> - GESTÃO JURÍDICA <small> - Cadastro de Notificações</small></h2></div>
	
            <form method="post" action="envia.notificacao.php" name="form" class="form-horizontal top-margin1" >
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Cadastro</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >TIPO</label>
                            <div class="col-sm-5">
                                <select name="tipo" id="tipo" class="form-control">
                                    <option value="">Selecione o tipo...</option>
                                    <?php
                                    $qr_tipo = mysql_query("SELECT * FROM tipos_notificacoes WHERE tipos_notificacoes_status = 1");
                                                    while($row_tipo = mysql_fetch_assoc($qr_tipo)):
                                                    ?>
                                    <option value="<?php echo $row_tipo['tipos_notificacoes_id']; ?>"> <?php echo $row_tipo['tipos_notificacoes_nome']; ?></option>                
                                                    <?php
                                                    endwhile;

                                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" >
                             <label for="select" class="col-sm-2 control-label hidden-print" >Nº DO DOCUMENTO:</label>
                                    <div class="col-sm-5">
                                        <input name="n_documento" type="text" class="form-control"/>
                                    </div>
                        </div>
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                            <div class="col-sm-5">
                                <select name="regiao" id="regiao" class="form-control">
                                    <option value="todos">TODOS</option>
                                    <?php
                                        $regioes->Preenhe_select_sem_master();
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-5">
                                <select name="projeto" id="projeto" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >Descrição</label>
                            <div class="col-sm-5">
                                <textarea name="descricao" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >Documentos Solicitados <br/> <a href="#" class="add_documento"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a></label>
                            <div class="col-sm-5">
                                <div id="documentos"></div>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >Responsável <br/> <a href="#" class="add_responsavel"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a></label>
                            <div class="col-sm-5">
                                <div id="responsaveis"></div>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >Data Limite <br/></label>
                            <div class="col-sm-5">
                                <input name="data_limite" class="form-control" type="text" id="data_limite"/>
                            </div>
                        </div>
                        
                    </div>
                    <div class="panel-footer text-right hidden-print controls">
                            <button type="submit" name="enviar" id="enviar" value="CADASTRAR" class="btn btn-primary"><span class="fa fa-filter"></span> Cadastrar</button>
                    
                            <div style="text-align:left">
                    <?php include('../../template/footer.php'); ?>
                    <div class="clear"></div></div>      
                    </div>
                </div>  
        </form>          
 
</div>
</body>
</html>