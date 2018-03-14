<?php 
if(empty($_COOKIE['logado'])){
   print "<script>location.href = '../../login.php?entre=true';</script>";
} else {
   include "../../conn.php";
   include "../../wfunction.php";
   include "../../classes/funcionario.php";
   $Fun = new funcionario();
   $Fun -> MostraUser(0);
   $Master = $Fun -> id_master;
   
   $usuario = carregaUsuario();
   
   $regiaoW = (!empty(($_GET['regiao']))) ? $_GET[regiao] : $usuario['id_regiao'];
}
?>
<html>
<head>
<title>Gerar PIS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body>
<table align="center" cellpadding="0" cellspacing="0" class="corpo" id="topo">
<tr>
	<td align="right"> <?php include('../../reportar_erro.php'); ?> </td>
</tr>

  <tr>
	<td align="center">
      <img src="imagens/logo_pis.jpg" width="357" height="150">
    </td>
  </tr>
 <?php $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiaoW' AND id_master = '$Master'");
       $regiao = mysql_fetch_assoc($qr_regiao);
	   
	   // Folhas de CLT
       $qr_folha = mysql_query("SELECT * FROM rh_folha WHERE status = '3' AND regiao = '$regiao[id_regiao]' ORDER BY mes ASC");
       $numero_folha = mysql_num_rows($qr_folha);
	   if(!empty($numero_folha)) { ?>
  <tr>
     <td align="center" bgcolor="#F1F1F1">
      <table cellpadding="0" cellspacing="0" border="0" width="95%">
         <tr>
           <td align="center">
        <a class="especial" onClick="document.getElementById('tabela1').style.display='';document.getElementById('oculta').style.display=''; document.getElementById('ver').style.display='none';" id="ver">FOLHAS DE PAGAMENTO <span class="destaque">CLT</span></a>
        <a class="especial_ativo" style="display:none;" onClick="document.getElementById('tabela1').style.display='none'; document.getElementById('oculta').style.display='none'; document.getElementById('ver').style.display='';" id="oculta">FOLHAS DE PAGAMENTO <span class="destaque">CLT</span></a>
           </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr id="tabela1" style="display:none;">
   <td>
     <table cellpadding="4" cellspacing="0" class="relacao" style="margin-top:10px;">
      <tr class="secao">
        <td width="25%">Folha</td>
        <td width="15%">Cria&ccedil;&atilde;o</td>
        <td width="13%">Mês</td>
        <td width="30%">Data de Pagamento</td>
        <td width="5%" align="center">CLTs</td>
        <td width="12%" align="center">GERAR</td>
      </tr>
      
    <?php while($folha = mysql_fetch_assoc($qr_folha)) { 
		  $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$folha[projeto]'");
		  $projeto = mysql_fetch_assoc($qr_projeto); ?>
          
    <tr class="linha_<? if($alternateColor++%2==0) { ?>um<? } else { ?>dois<? } ?>">
       <td><?php echo $folha['id_folha']." - ".$projeto['nome']; ?></td>
       <td><?php echo implode("/", array_reverse(explode("-", $folha['data_proc']))); ?></td>
       <td><?php $meses = array('ERRO','Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'); echo $meses[(int)$folha['mes']]; ?></td>
       <td><?php echo implode("/", array_reverse(explode("-", $folha['data_inicio']))); ?> até <?php echo implode("/", array_reverse(explode("-", $folha['data_fim']))); ?></td>
       <td align="center"><?=$folha['clts']?></td>
       <td align="center"><a href="pis.php?regiao=<?=$regiaoW?>&folha=<?=$folha['id_folha']?>&tipo=2" target="_blank" title="Gerar PIS | mês: <?=$meses[(int)$folha['mes']]?> | projeto: <?php echo $folha['id_folha']." - ".$projeto['nome']; ?>"><img src="imagens/pdf.jpg" width="25" height="25" alt="pdf"></a></td>
    </tr>
    
	<?php } ?>

    </table>
    
    <?php } 
	        // Folhas de Cooperados
	        $qr_folha_cooperado = mysql_query("SELECT * FROM folhas WHERE status = '3' AND regiao = '$regiao[id_regiao]' AND contratacao = '3' ORDER BY mes ASC");
	        $numero_folha_cooperado = mysql_num_rows($qr_folha_cooperado);
	        if(!empty($numero_folha_cooperado)) { ?>
       
      <tr>
    <td align="center" bgcolor="#F1F1F1">
      <table cellpadding="0" cellspacing="0" border="0" width="95%">
         <tr>
           <td align="center">
         <a class="especial" onClick="document.getElementById('tabela2').style.display='';document.getElementById('oculta2').style.display=''; document.getElementById('ver2').style.display='none';" id="ver2">FOLHAS DE PAGAMENTO <span class="destaque">COOPERADO</span></a>
        <a class="especial_ativo" style="display:none;" onClick="document.getElementById('tabela2').style.display='none'; document.getElementById('oculta2').style.display='none'; document.getElementById('ver2').style.display='';" id="oculta2">FOLHAS DE PAGAMENTO <span class="destaque">COOPERADO</span></a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr id="tabela2" style="display:none;">
         <td>
             <table cellpadding="4" cellspacing="0" class="relacao" style="margin-top:10px;">
                 <tr class="secao">
                     <td width="25%">Folha</td>
                     <td width="15%">Cria&ccedil;&atilde;o</td>
                     <td width="13%">Mês</td>
                     <td width="30%">Data de Pagamento</td>
                     <td width="5%" align="center">Cooperados</td>
                     <td width="12%" align="center">GERAR</td>
                 </tr>
                 
    <?php while($folha_cooperado = mysql_fetch_assoc($qr_folha_cooperado)) { 
		  $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$folha_cooperado[projeto]'");
		  $projeto = mysql_fetch_assoc($qr_projeto); ?>
    
    <tr class="linha_<? if($alternateColor++%2==0) { ?>um<? } else { ?>dois<? } ?>">
        <td><?php echo $folha_cooperado['id_folha']." - ".$projeto['nome']; ?></td>
        <td><?php echo implode("/", array_reverse(explode("-", $folha_cooperado['data_proc']))); ?></td>
        <td><?php $meses = array('ERRO','Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'); echo $meses[(int)$folha_cooperado['mes']]; ?></td>
        <td><?php echo implode("/", array_reverse(explode("-", $folha_cooperado['data_inicio']))); ?> até <?php echo implode("/", array_reverse(explode("-", $folha_cooperado['data_fim']))); ?></td>
        <td align="center"><?=$folha_cooperado['participantes']?></td>
        <td align="center"><a href="pis.php?regiao=<?=$regiaoW?>&folha=<?=$folha_cooperado['id_folha']?>&tipo=3" target="_blank" title="Gerar PIS | mês: <?=$meses[(int)$folha_cooperado['mes']]?> | projeto: <?php echo $folha_cooperado['id_folha']." - ".$projeto['nome']; ?>"><img src="imagens/pdf.jpg" width="25" height="25" alt="pdf"></a></td>
    </tr>
    
    <?php } ?>
    </table>
    <?php } ?>
      </td>
     </tr>  
     <?php if(empty($numero_folha) and empty($numero_folha_cooperado)) { ?>
     <tr>
        <td align="center"><p>&nbsp;</p><i>Nenhuma folha encontrada.</i></td>
     </tr>
   <?php } $qr_pis = mysql_query("SELECT * FROM pis WHERE regiao = '$regiao[id_regiao]' ORDER BY id ASC");
           $numero_pis = mysql_num_rows($qr_pis); ?>
      <tr>
         <td>&nbsp;</td>
      </tr>
      <tr class="historico">
          <td align="center" bgcolor="#F1F1F1">
      <table cellpadding="0" cellspacing="0" border="0" width="95%">
         <tr>
           <td align="center">
        <a class="especial" onClick="document.getElementById('tabela3').style.display='';document.getElementById('oculta3').style.display=''; document.getElementById('ver3').style.display='none';" id="ver3">HISTÓRICO DE DOCUMENTOS GERADOS</a>
        <a class="especial_ativo" style="display:none;" onClick="document.getElementById('tabela3').style.display='none'; document.getElementById('oculta3').style.display='none'; document.getElementById('ver3').style.display='';" id="oculta3">HISTÓRICO DE DOCUMENTOS GERADOS</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr id="tabela3" style="display:none;">
        <td>
        
        <?php if(!empty($numero_pis)) { ?>
        
  <table cellpadding="4" cellspacing="0" class="relacao" style="margin-top:10px;">
      <tr class="secao">
            <td width="25%">Data</td>
            <td width="30%">Folha</td>
            <td width="15%">Tipo</td>
            <td width="30%">Autor</td>
      </tr>
  <?php while($pis = mysql_fetch_assoc($qr_pis)) { ?>
    <tr class="linha_<? if($alternateColor++%2==0) { ?>um<? } else { ?>dois<? } ?>" style="font-size:12px;">
      <td>
	    <?php echo implode("/", array_reverse(explode("-", substr($pis['data'],0,10)))); echo " às "; echo substr($pis['data'],11,5); ?>
      </td>
      <td>
    <?php if($pis['tipo_contratacao'] == 2) {
		       $banco = "rh_folha"; 
		  } elseif($pis['tipo_contratacao'] == 3) { 
			   $banco = "folhas"; 
		  }      
		  $qr_getfolha = mysql_query("SELECT * FROM $banco WHERE id_folha = '$pis[folha]' AND regiao = '$pis[regiao]'");
	      $getfolha = mysql_fetch_assoc($qr_getfolha);
		  $qr_getprojeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$getfolha[projeto]'");
		  $getprojeto = mysql_fetch_assoc($qr_getprojeto);
		  echo $getfolha['id_folha']." - ".$getprojeto['nome']; ?>
       </td>
       <td>
	       <? if($pis['tipo_contratacao'] == 2) {
		        echo "CLT"; 
		      } elseif($pis['tipo_contratacao'] == 3) { 
			    echo "Cooperado"; 
		      } ?>
       </td>
       <td>
		    <? $qr_autor = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$pis[autor]'");
	           $autor = mysql_fetch_assoc($qr_autor); 
		       echo $autor['nome']; ?>
       </td>
    </tr>
    <?php } ?>
    </table>
    
    <?php } else { ?>
    
    <p>&nbsp;</p><p align="center"><i>Nenhum documento gerado até o momento.</i></p>
    </td>
  </tr>
</table>
    <?php } ?>

    </td>
  </tr>
</table>
</body>
</html>