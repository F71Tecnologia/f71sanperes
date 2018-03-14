<?php 

include ("include/restricoes.php");
include "../conn.php";
include "../funcoes.php";



$regiao  = $_REQUEST['regiao'];
$id_user  = $_COOKIE['logado'];
$id_saida = $_REQUEST['id']; 
$regiao_prestador = $regiao;
$enc = $_REQUEST['enc'];

$qr_saida = mysql_query("SELECT * FROM saida WHERE id_saida = '$id_saida'");
$row_saida = mysql_fetch_assoc($qr_saida);


if(isset($_POST['atualizar'])){
 
$data_vencimento = implode('-',array_reverse(explode('/',$_POST['data_vencimento'])));
$especifica      = $_POST['descricao'];
$estorno          = $_POST['estorno'];
$descricao_estorno = $_POST['descricao_estorno'];
$valor_estorno_parcial = str_replace(",",".",str_replace('.','',$_POST['valor_estorno_parcial']));

$sql = "UPDATE saida SET data_vencimento = '$data_vencimento', especifica = '$especifica', estorno = '$estorno', estorno_obs = '$descricao_estorno', valor_estorno_parcial = '$valor_estorno_parcial'  WHERE id_saida = '$id_saida' LIMIT 1";
mysql_query($sql);
echo 'Aguarde...';
echo "<script>
 if (parent.window.hs) {
		var exp = parent.window.hs.getExpander();
		if (exp) {
                         setTimeout(function() {
			 exp.close();
                          parent.window.location.reload();
			}, 3000);
		}
	}
</script>
";
exit;
}



?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<title>Intranet - Financeiro</title>
<link href="style/estrutura.css" rel="stylesheet" type="text/css">
<link href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
<link href="style/estrutura.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css" media="screen" title="no title" charset="utf-8" />
<link href="../uploadfy/css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../uploadfy/scripts/swfobject.js"></script>
<script language="javascript" type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
<script type="text/javascript" src="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
<script type="text/javascript" src="../jquery/priceFormat.js"></script>
<link href="style/estrutura.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
    
$(function(){


$('#valor_estorno_parcial').priceFormat({
    prefix: '',
    centsSeparator: ',',
    thousandsSeparator: '.'
});

$('.dt_vencimento').datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true
                        });
        
        
$('#FileUp').uploadify({

				'uploader'       : '../uploadfy/scripts/uploadify.swf',
				'script'         : '../include/upload_financeiro.php?Ultimo_ID=<?php echo $id_saida; ?>',
				'folder'         : 'fotos',
				'buttonText'     : 'Anexo',
				'queueID'        : 'barra_upload',
				'cancelImg'      : '../uploadfy/cancel.png',			
				'width'          : 190,
				'height'	 : 80,
				'auto'           : true,
				'method'         : 'post',
 				'multi'          : true,
				'fileDesc'       : 'Gif, Jpg , Png e Pdf',                              
                                 
				 'onComplete'   : function(resposta){
                                                             
                                                                        console.log(respota);
									},

				'onError'         : function(event,queueID,fileObj,errorObj){
										alert("Atenção \n O arquivo não foi anexado.\n Erro: \n"+errorObj.type);
									},

				

			 	'onSelect'        : function(){
									$("#barra_upload").next().append('<span style="color:#F00;font-size:10px;">Clique em cadastrar para concluir o envio!</span>');								}	});	


     
        $("#FileUp_pg").uploadify({
		'uploader'       : '../uploadfy/scripts/uploadify.swf',
		'script'         : 'actions/upload.comprovante.pg.php?Ultimo_ID=<?php echo $id_saida; ?>',
		'folder'         : 'fotos',
		'buttonText'     : 'Comprovante',
		'queueID'        : 'barra_upload_pg',
		'cancelImg'      : '../uploadfy/cancel.png',               
		'width'          : 190,
		'height'	 : 80,
                'auto'           : true,
		'method'         : 'post',
		'multi'          : true,
		'fileDesc'       : 'Gif, Jpg , Png e pdf',
		'fileExt'        : '*.gif;*.jpg;*.png;*.pdf;',
                'onComplete'   : function(resposta){
                                                             
                                                                        
									}
              /*  'onError'         : function(event,queueID,fileObj,errorObj){
										alert("Atenção \n O arquivo não foi anexado.\n Erro: \n"+errorObj.type);
									}*/
		//'scriptData'	 : {Ultimo_ID : $('#id_saida').val()}
		
	});


//////////////ANEXOS/////////////////
$('li.excluir, li.excluir_pg').click(function(){
		
               var div        = $(this);         
               var id_arquivo   = div.attr('value');
               var tipo_anexo = div.attr('rel');
               
             
               
		if(window.confirm('TEM CERTEZA QUE DESEJA DELETAR ESTE ANEXO?')){
			$.post('actions/apaga.anexo.php',
			{id : id_arquivo,
                        tipo_anexo : tipo_anexo},
                    
			function(retono){
				if(retono == '0'){
					
                                        alert('Erro ao deletar anexo.');
					return false;
                                        
				}else{
					div.prev().fadeOut('slow');                                        
                                        div.remove();
                                        //window.location.reload();
				}
			}
			);
		}
	});
     
     
     $('#form').submit(function(){
     
     
     var barra_anexo = $('#barra_upload').html();
     var barra_comprovante = $('#barra_upload_pg').html();
     
     
    if((barra_anexo == "")  && (barra_comprovante == "" ) ){

            return true; 

    }else{
              if(barra_anexo != "" ){
                  $('#FileUp').uploadifySettings('scriptData',{Ultimo_ID: <?php echo $id_saida;?>});                                               
                  $('#FileUp').uploadifyUpload();
              }

     if(barra_comprovante != null && barra_comprovante != ""){
                  $('#FileUp_pg').uploadifySettings('scriptData',{Ultimo_ID: <?php echo $id_saida;?>});
                  $('#FileUp_pg').uploadifyUpload();
     }                       
     
       setInterval(function(){
                                        

                                            if($('#barra_upload').html() == ''){
                                                  return true; 
                                            }else
                                            if($('#barra_upload_pg').html() == null && $('#barra_upload_pg').html() == ""){
                                                  return true; 
                                            }      
                                    },5000);  
     
     
     }
    
     });
    
    $('select[name=estorno]').change(function(){

    if($(this).attr('checked') == false){
       $('.descricao_estorno').fadeOut();
       $('.valor_estorno_parcial').fadeOut();
       
    } else if($(this).val() == 1){
         $('.descricao_estorno').fadeIn();
          $('.valor_estorno_parcial').fadeOut();
    }else if($(this).val() == 2){
          $('.descricao_estorno').fadeIn();
          $('.valor_estorno_parcial').fadeIn();
    }else if($(this).val() == ''){
    $('.descricao_estorno').fadeOut();
    $('.valor_estorno_parcial').fadeOut();

    }
    });
    
     
  });
        
</script>

   <style type="text/css">


.tabela{
    font-size: 10px;   
}
.tabela tr{ height: 35px;}



a, a:link, a:active{

	margin:0px;

	font-family: Arial, Helvetica, sans-serif;

	font-size: 12px;

	color: #333;

	text-decoration: underline;

}

.anexos{
	width:500px;
	overflow:scroll;
}
.anexos ul{
	padding:0px;
	margin:0px;
	overflow:hidden;
}
.anexos ul li{
	float: left;
	list-style-type: none;
	margin: 0px 5px;
	border:solid #999 1px;
}
.anexos ul li.excluir {
	margin-left:-20px;
	padding:3px;
	color:#FFF;
	background-color:#D90000;
	font-weight:bold;
	cursor: pointer;
}
.anexos ul li.excluir_pg {
	margin-left:-20px;
	padding:3px;
	color:#FFF;
	background-color:#D90000;
	font-weight:bold;
	cursor: pointer;
}

.anexos ul li.excluir_andamento {
	margin-left:-20px;
	padding:3px;
	color:#FFF;
	background-color:#D90000;
	font-weight:bold;
	cursor: pointer;
}



#progressbar{
	overflow:auto;
	height:120px;
	border:solid #CCC 1px;
	background-color:#FFF;
	width: 330px;
	display: none;
}

.pdf{
    width:100px;
    height: 100px;
    background-image: url'image/File-pdf-32.png';
  display:block;
    
}

.erro_file{
    color:red;
    font-size: 17px;
    font-family: arial;
    font-weight: bold;
}

</style>
    
</style>
</head>

<body>    
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" id="form">
    <table align="center" class="tabela">
    
        <tr>
            <td colspan="2"><h3><?php echo $row_saida['nome']; ?></h3></td>
        </tr>
        
        <tr>
        <td>DATA DE VENCIMENTO:</td>
        <td>    <input type="text" name="data_vencimento" class="dt_vencimento" value="<?php echo implode('/',array_reverse(explode('-',$row_saida['data_vencimento'])))?>"/></label>
        </td>
    </tr>
    <tr>
        <td>DESCRIÇÃO:</td>
        <td>   
            <input type="text" name="descricao" class="descricao" value="<?php echo $row_saida['especifica']?>" size="30" />
        </td>       
    </tr>
    <tr>
      <td colspan="2">    
          
      <div class="anexos">
        ANEXOS DA SAÍDA            
           <ul>
    <?php

        
        $qr_saida_files = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row_saida[id_saida]'");
					$num_saida_files = mysql_num_rows($qr_saida_files);
					if(!empty($num_saida_files)):
						while($row_saida_files = mysql_fetch_assoc($qr_saida_files)):
							$link_encryptado = encrypt('ID='.$row_saida_files['id_saida'].'&tipo=0&id_saida_files');
							echo "<li>";
							if($row_saida_files['tipo_saida_file'] == '.pdf'){
								
								echo "
								<a href=\"view/comprovantes.php?$link_encryptado\" target=\"_blank\" class=\"pdf\">
								<img src=\"image/File-pdf-32.png\" border=\"0\" width=\"100\" height=\"100\" />
								</a>
								";
							}else{
                                                            echo "<a href=\"view/comprovantes.php?$link_encryptado\" target=\"_blank\"><img src=\"http://".$_SERVER['HTTP_HOST']."/intranet/classes/img.php?foto=../comprovantes/$row_saida_files[id_saida_file].$row_saida_files[id_saida]$row_saida_files[tipo_saida_file]&w=100&h=100\"/></a>";
							}
                                                        
							echo "</li>";
							echo "<li value=\"$row_saida_files[id_saida_file]\" class=\"excluir\" rel=\"anexo\">X</li>";
						endwhile;
						
					endif;
  
    ?>
    
           </ul>
      </div> 
              <div id="barra_upload"></div>           
              <input type="file" id="FileUp"/>
           </td>
    </tr>
    
    
    <tr>
      <td colspan="2">  
        <div>
           <div class="anexos">
               <h4>COMPROVANTES DE PAGAMENTO</h4>        
               <ul>
           <?php     
            $qr_saida_files_pg = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$row_saida[id_saida]'");
                $num_saida_files = mysql_num_rows($qr_saida_files_pg);


                if(!empty($num_saida_files)):
                        $sem_arquivo = 0;
                        while($row_saida_files_pg = mysql_fetch_assoc($qr_saida_files_pg)):
                            $link_encryptado = encrypt('ID='.$row_saida_files_pg['id_saida'].'&tipo=1');
                            $path_arquivo = "../comprovantes/$row_saida_files_pg[id_pg].$row_saida_files_pg[id_saida]_pg$row_saida_files_pg[tipo_pg]";
                            
                            if(file_exists($path_arquivo)){ 
                                $sem_arquivo = 1;
                                echo "<li>";   
                                if($row_saida_files_pg['tipo_pg'] == '.pdf'){
                                        echo "
                                        <a href=\"{$path_arquivo}\" target=\"_blank\" class=\"pdf\">
                                        <img src=\"image/File-pdf-32.png\" border=\"0\" width=\"100\" height=\"100\" />
                                        </a>
                                        ";
                                }else{
                                    echo "<a href=\"{$path_arquivo}\" target=\"_blank\"><img src=\"http://".$_SERVER['HTTP_HOST']."/intranet/classes/img.php?foto={$path_arquivo}&w=100&h=100\"/></a>";
                                }
                                echo "</li>";
                                echo "<li value=\"$row_saida_files_pg[id_pg]\" class=\"excluir_pg\" rel=\"comprovante\">X</li>";
                                
                             }
                         endwhile;
                         
                         if($sem_arquivo == 0){
                             echo "<p class='erro_file'>Arquivo não encontrado, por favor anexe um arquivo.</p>";
                         }						
                endif;
            ?>
               </ul>
            </div>
        </div>

        <div id="barra_upload_pg"></div>
        <input type="file" id="FileUp_pg"/>
      </td>
    </tr> 
    
   <tr>
                    <td>ESTORNO</td>
                    <td align="left">
                        <select name="estorno">
                            <option value="">Selecione...</option>
                            <option value=""></option>
                            <option value="1" <?php echo ($row_saida['estorno'] == 1)? 'selected="selected"' :''; ?>>INTEGRAL</option>
                            <option value="2" <?php echo ($row_saida['estorno'] == 2)? 'selected="selected"' :''; ?>>PARCIAL</option>
                        </select>
                    </td>
                </tr>
                <tr class="valor_estorno_parcial" style="display:<?php  echo ($row_saida['estorno'] == 2)?'': 'none';?>">
                    <td>Valor do estorno:</td> 
                    <td align="left"><input type="text" name="valor_estorno_parcial" id="valor_estorno_parcial" value="<?php echo number_format($row_saida['valor_estorno_parcial'],2,',', '.');  ?>" id="valor_estorno_parcial" onKeyDown="FormataValor(this,event,17,2)"/>  </td>
                        
                </tr>
                
                
                <tr class="descricao_estorno" style="display:<?php  echo ($row_saida['estorno'] != 0)?'': 'none';?>">
                    <td valign="top">DESCRIÇÃO DO ESTORNO:</td>
                    <td>
                        <textarea name="descricao_estorno" cols="30" rows="5" ><?php echo trim($row_saida['estorno_obs']);?></textarea>
                    </td>
                </tr>
        <td align="center">
            <input type="submit" name="atualizar" value="Atualizar"/>
             <input type="hidden" name="id" value="<?php echo $id_saida;?>" id="id_saida"/>
        </td>
    </tr>    
    </table>
</form>

</body>

</html>