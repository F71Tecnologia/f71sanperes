
<?php 
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include "../include/criptografia.php";
include("../../classes_permissoes/regioes.class.php");
include("../../wfunction.php");

$id_user   = $_COOKIE['logado'];

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master FROM funcionario WHERE id_funcionario = '$id_user'");
$row_funcionario   = mysql_fetch_array($query_funcionario);
$tipo_user         = $row_funcionario['tipo_usuario'];

$query_master      = mysql_query("SELECT id_master FROM regioes WHERE id_master = '$row_funcionario[id_master];'");
$id_master         = @mysql_result($query_master,0);

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


<!--<link href="../../adm/css/estrutura.css" type="text/css" rel="stylesheet"/>-->
<link href="../../js/highslide.css" rel="stylesheet" type="text/css"  /> 

<!--<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>-->
<script src="../../jquery/jquery.tools.min.js" type="text/javascript"></script>
<!--<script src="../../jquery/jquery.tools.min.js" type="text/javascript"></script>-->
<script src="../../js/abas_anos.js" type="text/javascript"></script>
<script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 

<script type="text/javascript"> 
    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
	
	</script>
</head>

<body>
     <?php include("../../template/navbar_default.php"); ?>
    <div class="container">
        <div class="page-header box-juridico-header"><h2><span class="glyphicon glyphicon-briefcase"></span> - GESTÃO JURÍDICA <small> - Listagem de Notificações</small></h2></div>
        <div class="panel panel-default">
        <div class="panel-heading text-bold hidden-print">Listagem</div>
        
        <div class="panel-body">
    
<?php

 
$qr_tipo = mysql_query("SELECT * FROM tipos_notificacoes WHERE 1");
while($row_tipos  = mysql_fetch_assoc($qr_tipo)):
  
  	$qr_notificacoes = mysql_query("SELECT * FROM notificacoes WHERE tipos_notificacoes_id = '$row_tipos[tipos_notificacoes_id]' AND notificacao_status= 1");
	
	if(mysql_num_rows($qr_notificacoes) != 0){
		?>
        
        <a href="#" class="titulo_ano btn btn-default"><?php echo $row_tipos['tipos_notificacoes_nome']?></a>
               
       
        <table class="table table-striped table-hover table-bordered text-sm valign-middle" style="display:none;" >      
            <tr class="secao_nova" >
              <td width="30">Nº</td>
              <td></td>
              <td width="130">REGIÃO</td>
              <td width="200">PROJETO</td>
              <td  width="400">RESPONSÁVEL</td>
              <td width="90">DATA LIMITE</td>
              <td width="50">EDITAR</td>
              <td width="50">EXCLUIR</td>
            </tr>
        
<?php	
	while($row_notificacao =  mysql_fetch_assoc($qr_notificacoes)):
			
			
			
			if($row_notificacao['id_projeto'] != 'todos') {
				$nome_regiao  = @mysql_result(mysql_query("SELECT regiao  FROM regioes WHERE id_regiao = '$row_notificacao[id_regiao]'"),0) or die(mysql_error());	
			} else {
				$nome_regiao = $row_notificacao['id_projeto'];
			}
			
			if($row_notificacao['id_projeto'] != 'todos') {				
				$nome_projeto = @mysql_result(mysql_query("SELECT nome  FROM projeto WHERE id_projeto = '$row_notificacao[id_projeto]'"),0) or die(mysql_error());	
			} else {
				$nome_projeto = $row_notificacao['id_projeto'];
				}
				
				
		///	$nome_funcionario_cad = mysql_result(mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$row_notificacao[notificacao_user_cad]'"),0);	
			
			 $class = (($i++ % 2) == 0)? 'class="linha_um"' : 'class="linha_dois"';?>
			
<tr <?php echo $class;?>>
  <td><?php echo $row_notificacao['notificacao_numero']?></td>
  <td><a href="action.ver_anexos.php?id_noti=<?php echo $row_notificacao['notificacao_id']; ?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )"> <img src="../../imagens/ver_anexo.gif" width="20" height="20"/> </a></td>
  <td><?php echo $nome_regiao; ?></td>
  <td><?php echo $nome_projeto; ?></td>
  <td><?php
                $qr_responsavel = mysql_query("SELECT * FROM  notific_responsavel_assoc WHERE notificacoes_id =  '$row_notificacao[notificacao_id]'");
				
				while($row_responsavel = mysql_fetch_assoc($qr_responsavel)):
					$array_responsavel[] = @mysql_result(mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$row_responsavel[funcionario_id]'"),0);
				endwhile;
				
				if(sizeof($array_responsavel) <= 1){
					echo $array_responsavel[0];
				} else {
					
					echo implode(', ',$array_responsavel);
				}
				?>
    </td>
  	<td><?php echo implode('/',array_reverse(explode('-',$row_notificacao['notificacao_data_limite'])));?></td>
	<td><a href="editar.php?id_noti=<?php echo $row_notificacao['notificacao_id']; ?>"><img src="../../imagens/editar_projeto.png" width="30" height="30"/></a></td>
    <td><a href="excluir.php?id_noti=<?php echo $row_notificacao['notificacao_id']; ?>"><img src="../../imagens/desativar.png" width="30" height="30"/></a></td>  
</tr>

<?php

unset($array_responsavel);
	endwhile;
	
			echo '<tr>
			<td colspan="7">&nbsp;</td>
			</tr>
			</table>';
	}
endwhile;
?>

</div>
    </div>

</div>
    

   
    <div class="panel-footer text-right hidden-print controls">
        
        <a href="../index.php"  class="voltar btn btn-success">VOLTAR</a>
        

        <!--<button type="submit" name="enviar" id="enviar" value="CADASTRAR" class="btn btn-primary"><span class="fa fa-filter"></span> Cadastrar</button>-->

        <div style="text-align:left">
        <?php include('../../template/footer.php'); ?>
        <div class="clear"></div></div>      
    </div>


</body>
</html>
