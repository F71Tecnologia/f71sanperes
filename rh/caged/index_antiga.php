<?php 
require_once("../../conn.php");
$anos = array('2010', '2011', '2012', '2013', '2014');

function data($str){
	return(implode("/",array_reverse(explode('-',$str))));
}

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]' ") or die(mysql_error());
$row_user = mysql_fetch_assoc($qr_user);

$qr_master   = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);

$qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = '$row_master[id_master]'");
while($row_regioes = mysql_fetch_assoc($qr_regioes)):

$regioes[] = $row_regioes['id_regiao'];


endwhile;

$regioes = implode(',' , $regioes);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>CAGED</title>
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
$(function(){
	
	$('.titulo_mes').click(function(){
	
	var tabela  = $(this).next();	
			
	if(tabela.css('display') == 'none') {
	
			$('.tabela').hide();
			tabela.show();
	
		
	} else {
			tabela.hide();
		
	}
		/*$('.titulo_mes a').not(this).parent().next().slideUp();
		$('.titulo_mes a').not(this).parent().next().next().slideUp();
		$(this).parent().next().slideToggle();
		$(this).parent().next().next().slideToggle();*/
	});
});
</script>
<link type="text/css" href="css/estilo.css" rel="stylesheet" />
<style>
.tabela{ display:none; }
</style>

</head>
<body>
<div id="conteiner">
<div style="float:right;">

 <?php include('../../reportar_erro.php'); ?>
</div>

<div id="top" style="width:100%; height:auto; display:block; text-align:center; margin-bottom: 15px;">
  	<img src="../../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"  width="120" height="100"/>
  </div>
  
<div style="clear:right;"></div>
  <div id="top">
  	<img src="imagens/barra_caged_degrade.jpg" width="810" height="73" />
  </div>
  <div id="base">
  <?php foreach($anos as $ano){
	  
	  
	unset($qr_meses);
	$qr_meses = mysql_query("SELECT * FROM ano_meses ORDER BY num_mes ASC");
	  while($row_mes = mysql_fetch_assoc($qr_meses)){
		  
		  
	  	$qr_clt_demitidos = mysql_query("SELECT * FROM rh_clt WHERE YEAR(data_demi) = '$ano' AND MONTH(data_demi) = '$row_mes[num_mes]' AND status IN('60','61','62','81','100','80','63') AND id_regiao IN($regioes) ORDER BY nome ASC;");
		$qr_clt_admitidos = mysql_query("SELECT * FROM rh_clt WHERE YEAR(data_entrada) = '$ano' AND MONTH(data_entrada) = '$row_mes[num_mes]' AND (status != '60' OR status != '61' OR status != '62' OR status != '81' OR status != '100' OR status != '80' OR status != '63' ) AND id_regiao IN($regioes) ORDER BY nome ASC;");
		$num_clt_demitidos = mysql_num_rows($qr_clt_demitidos);
		$num_clt_admitidos = mysql_num_rows($qr_clt_admitidos);
		
		
		
		if(!empty($num_clt_admitidos) or !empty($num_clt_demitidos) ){
			
			
			if($ano != $ano_anterior) { echo "<div class='titulo_ano'>$ano</div>" ; }
			
  ?>
 	<div class="titulo_mes"><?=$row_mes['nome_mes']?>&nbsp;
     	<a style="cursor:pointer;"><img src="../folha/sintetica/seta_dois.gif" width="9" height="9" /></a>
    &nbsp;<a href="relacao.php?mes=<?=$row_mes['num_mes']?>&ano=<?=$ano?>"><img src="imagens/TXT.png" width="30" height="30" border="0" /></a></div>
 
   	  <table width="100%" cellpadding="5" cellspacing="1" bgcolor="#CCCCCC" class="tabela">
    
        	<tr>
           	  <td align="center" colspan="7" class="titulo_dados">Admiss&otilde;es</td>
            </tr>
            
           <tr bgcolor="#FFFFFF">
            	<td width="3%"><b>ID CLT</b></td>
                <td width="22%"><b>Nome</b></td>
                <td width="8%"><b>Região</b></td>
                <td width="20%" colspan="2"><b>Projeto</b></td>
                <td width="34%"><b>Tipo de admissão</b></td>
                <td width="13%"><b>Data de admiss&atilde;o</b></td>
            </tr>
           
       <?php
	   $ano_anterior  = $ano;
		}
		
	if(!empty($num_clt_admitidos)){
	    while($row_admitidos = mysql_fetch_assoc($qr_clt_admitidos)){ ?>
            <tr bgcolor="<? if($alternateColor++%2==0) { ?>#FBFBFB<? } else { ?>#FFFFFF<? } ?>">
              <td><?=$row_admitidos['id_clt']?></td>
              <td><?=$row_admitidos['nome']?></td>
              <td>
            <?php 
                    $qr_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_admitidos[id_regiao]' ;");
                    echo mysql_result($qr_regiao,0);
            ?>
              </td>
              <td colspan="2">
				<?php 
                $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$row_admitidos[id_projeto]';");
                echo mysql_result($qr_projeto,0);
                ?>
                </td>
              <td>
              	<?php 
				$tipo_admissao = array(
								 10 => "Primeiro emprego",
								 20 => "Reemprego",
								 25 => "Contrato por prazo determinado",
								 35 => "Reintegra&ccedil;&atilde;o",
								 70 => "Transferência da entrada"
								 );
                                
				foreach($tipo_admissao as $chave => $nome):
					if($row_admitidos['status_admi'] == $chave){
						echo $nome;
						break;
					}
				endforeach;
				?>
              </td>
              <td><?=data($row_admitidos['data_entrada'])?></td>
            </tr>
            <?php }?>
 
  
    <?php }// fecha if admitidos?>
    
    
    
    
    
    
    <?php if(!empty($num_clt_demitidos)){?>
   

        	<tr>
           	  <td align="center" colspan="7" class="titulo_dados">Demiss&otilde;es</td>
            </tr>
        	 <tr bgcolor="#FFFFFF">
            	<td width="3%"><b>ID CLT</b></td>
                <td width="20%"><b>Nome</b></td>
                <td width="10%"><b>Região</b></td>
                <td width="20%"><b>Projeto</b></td>
                <td width="17%"><b>Tipo Demi.</b></td>
                <td width="16%"><b>Data de admiss&atilde;o</b></td>
                <td width="14%"><b>Data de Demiss&atilde;o</b></td>
            </tr>
          <?php while($row_demitidos = mysql_fetch_assoc($qr_clt_demitidos)){ ?>
             <tr bgcolor="<? if($alternateColor2++%2==0) { ?>#FBFBFB<? } else { ?>#FFFFFF<? } ?>">
            	<td><?=$row_demitidos['id_clt']?></td>
                <td><?=$row_demitidos['nome']?></td>
                <td>
				<?php 
			  		$qr_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_demitidos[id_regiao]';");
					echo mysql_result($qr_regiao,0);
			  	?>
                </td>
                <td>
				<?php 
                $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$row_demitidos[id_projeto]';");
                echo mysql_result($qr_projeto,0);
                ?>
                </td>
                <td>
				<?php
				$qr_tipodemi = mysql_query("SELECT especifica FROM rhstatus WHERE codigo = '$row_demitidos[status]';");
				echo mysql_result($qr_tipodemi,0);
                ?>
                </td>
                <td><?=data($row_demitidos['data_entrada'])?></td>
                <td><?=data($row_demitidos['data_demi'])?></td>
            </tr>
            <?php }?>
       
  
<?php }// fecha if demitidos 	
 ?>   
</table>
 
 <?php
 } // while meses
  }// for each?>
  </div>
</div>
</body>
</html>