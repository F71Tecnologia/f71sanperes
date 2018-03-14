<?php
if(empty($_COOKIE['logado'])){
	print "Efetue o Login<br><a href='login.php'>Logar</a> ";
	exit;
}

include "conn.php";
include "classes/regiao.php";
include "classes/funcionario.php";


$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$master = $row_user['id_master'];
$id = $_REQUEST['id'];

$USER = new funcionario();


//ajax excluir
if(isset($_POST['excluir']) and $_POST['excluir'] == 1){   
    
    mysql_query("UPDATE unidade SET status_reg = 0 WHERE id_unidade = '$_POST[id_unidade]' LIMIT 1");
    exit;
}


?>
<html><head><title>:: Intranet ::</title>

<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<meta http-equiv='Cache-Control' content='No-Cache'>
<meta http-equiv='Pragma'        content='No-Cache'>
<meta http-equiv='Expires'       content='No-Cache'>

<meta http-equiv='Expires' content='Fri, Jan 01 1900 00:00:00 GMT'/>   
<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate'/>   
<meta http-equiv='Cache-Control' content='post-check=0, pre-check=0'/>   
<meta http-equiv='Pragma' content='no-cache'/>

<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="js/lightbox.js"></script>

<!-- UPLOADFY -->
<link href="uploadfy/css/default.css" rel="stylesheet" type="text/css" />
<link href="uploadfy/css/uploadify.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="js/abas_anos.js"></script>
<script type="text/javascript" src="uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
<script type="text/javascript">
$().ready(function(){
	$("#barra_upload").hide();
	$('#reenvio').uploadify({
				'uploader'       : 'uploadfy/scripts/uploadify.swf',
				'script'         : 'include/upload_financeiro.php',
				'folder'         : 'fotos',
				'buttonText'     : 'Reenviar anexo',
				'queueID'        : 'barra_upload',
				'cancelImg'      : 'uploadfy/cancel.png',
				'auto'           : true,
				'method'         : 'post',
 				'multi'          : false,
				'fileDesc'       : 'Gif, Jpg e Png',
				'fileExt'        : '*.gif;*.jpg;*.png;',
				'scriptData'    : { 
										'Ultimo_ID' : '<?=$_GET['saida']?>'
									},
				'onSelect'       : function(a,queueID,fileObj){
											$("#barra_upload").show();
											$('#reenvio').uploadifySettings('scriptData', {
																  'Tipo' : fileObj.type,
																  'Ultimo_ID' : '<?=$_GET['saida']?>'
																  });
									
										
									
									},
				'onComplete'     : function(a,b,c,d){
											$("#barra_upload").hide();
											window.location.reload();																					
									}
						   
	});


	
});
</script>
<!-- UPLOADFY -->
<script type="text/javascript" src="js/highslide-with-html.js"></script>
<link rel="stylesheet" type="text/css" href="js/highslide.css" />

<script src='jquery/jquery-1.4.2.min.js' type='text/javascript'></script>
<link rel="stylesheet" href="js/lightbox.css" type="text/css" media="screen" />
<link href="adm/css/estrutura.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
    hs.graphicsDir = 'images-box/graphics/';
    hs.outlineType = 'rounded-white';
    
    $(function(){
        $('a.excluir').click(function(){
            
            var id_unidade = $(this).attr('rel');
            
            if(confirm('Deseja excluir esta unidade?')){
                
                $.post("visualizar_unidade.php", {id_unidade: id_unidade, excluir : 1})
                        .done(function(data){ 
                            alert("A unidade foi desativada!")
                        })
            }
            
        })
        
    })
  
</script>
<script type='text/javascript' src='js/ramon.js'></script>
</head>
<body >
	<div id="corpo">
    	<div id="conteudo">
        
        	 <div class="right"><?php include('reportar_erro.php'); ?></div>
       			 <div class="clear"></div>
                 
        		<img src="imagens/logomaster<?php echo $master?>.gif"/>
				<h3>VISUALIZAR UNIDADES</h3>
              
<?php


$regiao = $_REQUEST['regiao'];
$array_status = array('1' => 'PROJETOS ATIVOS', 0 => 'PROJETOS INATIVOS');


foreach($array_status as $status => $nome_status) {
	
	if($status == 1){
		$result_pro = mysql_query("Select * from projeto where id_regiao = '$regiao' AND status_reg = '$status' ORDER BY nome, status_reg ASC");
				
	} else {
		$result_pro = mysql_query("Select * from projeto where id_regiao = '$regiao' AND status_reg != 1 ORDER BY nome, status_reg ASC");
		
	}
				if($_GET['sucesso']) { 
					echo 'Editado com sucesso!';
				} ?>
				
				
				
				<?php
				if($status != $status_anterior){
						
					echo '<h3 class="titulo">'.$nome_status.'</h3>';
						
					}
					
				while ($row_pro = mysql_fetch_array($result_pro)){
				                   
				$result = mysql_query("Select * from unidade where id_regiao = '$regiao' and campo1 = '$row_pro[0]' and status_reg = '1' ORDER BY unidade");
				
				if(mysql_num_rows($result) != 0) {
					
					
					
					
							?>
							
							
								<a href="#" class="titulo_ano"> <?=$row_pro['nome']?></a>
							    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" class="folhas" style="display:none;">
							  <tr class="titulo_tabela" bgcolor="#CCC">
							    <td width="8%" align="center">Cód</td>
							    <td width="33%" align="center">Unidade</td>
							    <td width="14%" align="center">Telefone</td>
							    <td width="21%" align="center">Endereço</td>
							    <td width="17%" align="center">Responsavel</td>
							    <td width="6%" align="center">Edição</td>
							    <td width="6%" align="center">Excluir</td>
							  </tr>
							
							<?php
							
							while ($row = mysql_fetch_array($result)){
							$class = (($i++ % 2) == 0)? 'class="linha_um"' : 'class="linha_dois"';
							
							
							
							
							?>
							  <tr  <?=$class?> height="25">
							    <td align="center"><?=$row[0]?></td>
							    <td><?=$row['unidade']?>
							    </td>
							    <td align="center"><?=($row['tel'] == "(  )" or $row['tel'] == "") ? "<span style='font-size:10px;'><i>não informado</i></span>" : "$row[tel]" ?></td>
							    <td><span title="<?=$row['local']?>" >
								
								<?php
							    
								echo strtoupper(substr($row['local'],0,25));
								echo (strlen($row['local']) > 25) ? "..." : "";
								
								?>
							    </span>
							    </td>
							    <td><?=($row['responsavel'] == "") ? "<span style='font-size:10px;'><i>não informado</i></span>" : "$row[responsavel]"?></td>
							    <td><a href="editar_unidade.php?id=<?=$row[0]?>" title="EDITAR"> <img src="imagens/editar_projeto.png" /></a></td>
                                                            <td><a href="#" class="excluir" rel="<?php echo $row[0]?>"><img src="imagens/excluir.png" width="15" height =" 15"/></a></td>
                                                          </tr>
							
							<?php  
							} // Fim while de unidades
				
				}
				
				?>
				
				
				</table>
				<?php	
				
				$status_anterior = $status;		
				
				} // Fim while de projetos
				
		
}
?>
  
	</div>
    </div>
</body>
</html>