<?php
if(empty($_COOKIE['logado'])) {
   print "<script>location.href = '../../login.php?entre=true';</script>";
} else {
   include('../../conn.php');
   include('../../classes/funcionario.php');
   include('../../wfunction.php');
   $Fun = new funcionario();
   $Fun -> MostraUser(0);
   $Master = $Fun -> id_master;
}

if($_GET['excluir'] == true) {
	if($_GET['tipo'] == 1) {
		mysql_query("DELETE FROM sefip WHERE mes = '$_GET[mes]' AND ano = '$_GET[ano]' AND tipo_sefip = '1' LIMIT 1");
	} else {
		mysql_query("DELETE FROM sefip WHERE regiao = '$_GET[regiao]' AND projeto = '$_GET[projeto]' AND mes = '$_GET[mes]' AND ano = '$_GET[ano]' AND folha = '$_GET[folha]' AND tipo_sefip = '2' LIMIT 1");
	}
	header("Location: index.php?regiao=$_GET[regiao]");
}


$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);

$qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = '$row_user[id_master]'");
while($row_regiao = mysql_fetch_assoc($qr_regioes)):


$regioes[] = $row_regiao['id_regiao'];


endwhile;
$regioes  = implode(',', $regioes);

?>
<html>
<head>
<title>Gerar SEFIP</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="../../novoFinanceiro/style/form.css"/>
<!-- higtslide -->
<script type="text/javascript" src="../../js/highslide-with-html.js"></script>
<link href="../../js/highslide.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    hs.graphicsDir = '../../images-box/graphics/';
	hs.outlineType = 'rounded-white';
	hs.showCredits = false;
	hs.wrapperClassName = 'draggable-header';
</script>
<!-- higtslide -->
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<link href="../../novoFinanceiro/style/form.css" rel="stylesheet" type="text/css">
<script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="../../jquery/jquery.tools.min.js" type="text/javascript"></script>
<script language="javascript">
function Reload() {
	setTimeout("location.href = '<?=$_SERVER['PHP_SELF']?>?regiao=<?=$_GET['regiao']?>&aberto=true'",5000);
}
$(function(){
	
        
        
});
</script>
<style type="text/css">
.ano {
	text-align:center;
}
.ano table {
	display:none;
}
.folha_mes {
	cursor:pointer;
	width:100%;
}
.titulo {
	background-color:#F1F1F1; cursor:pointer; font-size:13px; padding:4px 0px 4px 0px; width:100%; text-align:center; font-weight:bold; margin-top:10px; clear:both;
}
.tooltip {
	display:none; background-color:#fff; border:1px solid #777; padding:5px; font-size:13px; -moz-box-shadow:2px 2px 11px #666; -webkit-box-shadow:2px 2px 11px #666; text-align:left; line-height:30px;
}
.tooltip a {
	color:#222; text-decoration:none;
}
.dados {
	font-size:13px;
}
.cabecalho {
	font-weight: bold; font-size:13px;
}
.ano_recisao{
	padding:4px; margin:5px 0px; background-color:#F1F1F1; text-align:center; cursor:pointer;
}
.mes_recisao{
	padding:0px 10px; margin: 3px 0px; cursor:pointer;  background-color:#F9F7F7;
}
.mes_focus{
		padding:0px 10px; margin: 3px 0px; cursor:pointer;  background-color:#CCC;
}
.recindidos table{
	font-size:12px;
}


#form{

font-size:13px;	
background-color: #F7F7F7;
}

#form h4{
text-align:center;	
}



/**********************************/

.nome_mes{
    
    border: 1px solid #000;    
   
}
.box_mes{
    background-color: #F1F1F1;
  
    margin-left: 5px;
    margin-right: 5px;
    padding: 5px;    
}

</style>
</head>
<body>
<table align="center" cellpadding="0" cellspacing="0" class="corpo" id="topo">
<tr>
    	<td align="right"><?php include('../../reportar_erro.php'); ?></td>
    </tr>
  <tr>
	<td align="center">
      <img src="imagens/logo_sefip.jpg" width="357" height="150">
    </td>
  </tr>
  <tr>
    <td>        
      
                   
            
	 <?php 
         $qr_folha = mysql_query("SELECT rh_folha.projeto, rh_folha.regiao, rh_folha.id_folha, projeto.nome AS nome_projeto, regioes.regiao AS nome_regiao, 
                                                    COUNT( rh_folha_proc.id_clt ) AS total_participante,rh_folha.terceiro, rh_folha.tipo_terceiro, rh_folha.mes, rh_folha.ano,
                                                    
                                                     IF(terceiro = 1,
                                                                    (CASE tipo_terceiro 
                                                                    WHEN 1 THEN 'Décimo Terceiro - 1ª Parcela'
                                                                    WHEN 2 THEN 'Décimo Terceiro - 2ª Parcela'
                                                                    WHEN 3 THEN 'Décimo Terceiro Integral'
                                                                    END)
                                                                    , ''
                                             ) as tipo_folha

                                                    FROM rh_folha
                                                    INNER JOIN projeto ON projeto.id_projeto = rh_folha.projeto
                                                    INNER JOIN regioes ON regioes.id_regiao = rh_folha.regiao
                                                    INNER JOIN rh_folha_proc ON rh_folha_proc.id_folha = rh_folha.id_folha
                                                    WHERE rh_folha.status = '3'
                                                    AND rh_folha.regiao IN($regioes)                                                  
                                                    GROUP BY rh_folha_proc.id_folha
                                                    ORDER BY  rh_folha.ano, rh_folha.mes ");
         
			   $total_folha = mysql_num_rows($qr_folha);
			   
			   if(!empty($total_folha)) {    
                               
                               while($folha = mysql_fetch_assoc($qr_folha)) {
                                   
                                 if($ano_anterior != $folha['ano']){                                                                              
                                     echo '<div class="titulo">FOLHAS DE PAGAMENTO <span class="destaque">'.$folha['ano'].'</span></div>';
                                   }
                                   
                                   if($mes_anterior != $folha['mes']){                                      

                                                if($folha['terceiro'] == 1){ 

                                                    echo '<div  class="nome_mes">'.$folha['tipo_folha'].' </div>'; 
                                                    echo '<div class="box_mes">';

                                                } else {
                                                       echo '<div  class="nome_mes">'.mesesArray($folha['mes']).' </div>';
                                                       echo '<div class="box_mes">';
                                                }
                                       
                                    }
                                    
						@$nome_projeto  = $folha['nome_projeto'];	
						$projeto        = $folha['projeto'];					                  	
						$nome_regiao    = $folha['nome_regiao'];
						$regiao 	= $folha['regiao'];
                                                $mes            = $folha['mes'];
                                                $ano            = $folha['ano'];
						
                                                
						$total_participantes = $folha['total_participante'];
                                                $total_geral_participantes += $total_participantes;
						
						$qr_verifica_sefip = mysql_query("SELECT * FROM sefip WHERE mes = '$mes' AND ano = '$ano' AND regiao = '$regiao' AND projeto = '$projeto' AND folha = '$folha[id_folha]' AND tipo_sefip = '2'"); 
		  			 	$verifica_sefip    = mysql_num_rows($qr_verifica_sefip);
						
						if(!empty($verifica_sefip)) {
						
                                                echo "<a href='arquivos/".$regiao."_".$projeto."_".$mes."_".$ano.".re' target='_blank' title='Visualizar SEFIP'>".$nome_projeto." - ".$nome_regiao." (".$total_participantes.")</a> <a href='index.php?excluir=true&regiao=
                                                                ".$regiao."&projeto=".$projeto."&mes=".$mes."&ano=".$ano."&folha=".$folha['id_folha']."' title='Excluir SEFIP' style='color:#C30'>excluir</a><br>";

                                                } else {

                                                echo "<a href='sefiptexto2.php?mes=".$mes."&ano=".$ano."&regiao=".$regiao."&projeto=".$projeto."&folha=".$folha['id_folha']."' onClick='return false' class='dataSefip' title='Gerar SEFIP'>".$nome_projeto." - ".$nome_regiao." (".$total_participantes.")</a>";
                                                echo "<div >
                                                        <input type='text' name='date' class='date' size='7'/>
                                                                   <a href='#' class='confirmadata' style='color:#993'>Criar SEFIP</a>
                                                      </div><br>";

                                                }
					 ?> 
                    <br></span>

			   <?php $qr_verifica_sefip = mysql_query("SELECT * FROM sefip WHERE mes = '$mes' AND ano = '$ano' AND tipo_sefip = '1'"); 
		  			 $verifica_sefip    = mysql_num_rows($qr_verifica_sefip);

					 if(!empty($verifica_sefip)) {
						
						echo "<a href='arquivos/".$mes."_".$ano.".re' target='_blank'>Visualizar SEFIP</a> <a href='index.php?excluir=true&mes=".$mes."&ano=".$ano."&tipo=1' title='Excluir SEFIP' style='color:#C30'>excluir</a><br>";
}
                         
         
           
         $ano_anterior = $folha['ano'];      
        
         
          if($mes_anterior == $folha['mes']){ 
          
                echo   '</div>';
           }
          $mes_anterior = $folha['mes'];
          unset($total_geral_participantes);           
               } ///fim while
                            
               }  ?>
  </td>
 </tr>
 <!-- GRRF criado por maikom james dia 01/10/2010  -->

 <tr>
 	<td>
	<?php 
	$query_rescisao = mysql_query("SELECT * FROM rh_recisao");
	
    ?> 
    <?php for($ano=2009; $ano<=date('Y'); $ano++):?>
    	<div class="ano_recisao">GRRF
        	<span class="destaque"><?=$ano?></span>
        </div>
    <div class="meses_recisao" style="display:none;">
    <?php foreach($meses as $nome_mes => $num_mes): ?>
    <?php $qr_recisao = mysql_query("
			SELECT 
			rh_recisao.id_clt,rh_recisao.nome,
			regioes.id_regiao,regioes.regiao,
			projeto.id_projeto,projeto.nome as nome_projeto
			 FROM (rh_recisao INNER JOIN regioes
			ON rh_recisao.id_regiao = regioes.id_regiao)
			INNER JOIN projeto 
			ON rh_recisao.id_projeto = projeto.id_projeto
			WHERE MONTH(rh_recisao.data_demi) = '$num_mes'
			AND YEAR(rh_recisao.data_demi) = '$ano'
			AND rh_recisao.status = '1'
			AND regioes.id_regiao IN($regioes)
			ORDER BY regioes.id_regiao ASC
		");
		
		
		?>
    	<div class="mes_recisao"> <?=$nome_mes?>	</div>
        
        
        <div class='recindidos' style="display:none;">
            
                    <?php while($row_recisao = mysql_fetch_assoc($qr_recisao)): ?>
                    <?php $qr_grrf = mysql_query("SELECT * FROM grrf WHERE id_clt = '$row_recisao[id_clt]' AND mes = '$num_mes' AND ano = '$ano' AND id_projeto = '$row_recisao[id_projeto]' AND id_regiao = '$row_recisao[id_regiao]'");
                                    $num_grrf = mysql_num_rows($qr_grrf);
                            ?>
                    <table width="100%" cellpadding="2" cellspacing="0">
                            <tr <?php if(!empty($num_grrf)){ echo "bgcolor=\"#FF8A8A\""; } ?> >
                                    <td width="23%"><?=$row_recisao['id_regiao'].' - '.$row_recisao['regiao']?></td>
                                <td width="23%"><?=$row_recisao['id_projeto'].' - '.$row_recisao['nome_projeto']?></td>
                                <td width="23%"><?=$row_recisao['id_clt'].' - '.$row_recisao['nome']?></td>
                                <td width="10%" align="right"><a href="view/confirmacao.grrf.php?mes=<?=$num_mes?>&ano=<?=$ano?>&clt=<?=$row_recisao['id_clt']?>&regiao=<?=$row_recisao['id_regiao']?>&projeto=<?=$row_recisao['id_projeto']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe'} )"><img border="0" src="../imagensrh/recisao.jpg" ></a></td>
                            </tr>
                    </table>
                    <?php endwhile; ?>
                    </div>
                <?php endforeach;?>       
        
    </div>
    <?php endfor;?>
    </td>
 </tr>
 <!-- GRRF criado por maikom james dia 01/10/2010  -->
</table>
</body>
</html>