<?php 
if(empty($_COOKIE['logado'])){
	print "<script>location.href = '../../login.php?entre=true';</script>";
} else {
	include "../../conn.php";
	include "../../classes/funcionario.php";
	$Fun = new funcionario();
	$Fun -> MostraUser(0);
	$Master = $Fun -> id_master;
}
?>
<html>
<head>
<title>Relat&oacute;rio de IRRF</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="../../jquery/jquery.tools.min.js" type="text/javascript"></script>
<script language="javascript">
function Reload() {
	setTimeout("location.href = '<?=$_SERVER['PHP_SELF']?>?regiao=<?=$_GET['regiao']?>&aberto=true'",5000);
}
$(function(){
	$('.ano').click(function(){
		$('.ano').not(this).find('table').hide();
		$('.ano').find('.titulo').css('background-color','#F1F1F1');
		$(this).find('.titulo').css('background-color','#bbb');
		$(this).find('table').toggle();
	});
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
	cursor:pointer; width:100%;
}
.titulo {
	background-color:#F1F1F1; cursor:pointer; font-size:13px; padding:4px 0px 4px 0px; width:100%; text-align:center; font-weight:bold; margin-top:10px; clear:both;
}
</style>
</head>
<body>
<table align="center" cellpadding="0" cellspacing="0" class="corpo" id="topo">
	<tr>
        <td align="right">
            <?php include('../../reportar_erro.php'); ?>
        </td>
   </tr>
   
  <tr>
	<td align="center">
      <img src="imagens/logo_ir.jpg" width="500" height="150">
    </td>
  </tr>
  <tr>
    <td>
 <?php $meses = array('Janeiro' => '01','Fevereiro' => '02','Março' => '03','Abril' => '04','Maio' => '05','Junho' => '06','Julho' => '07','Agosto' => '08','Setembro' => '09','Outubro' => '10','Novembro' => '11','Dezembro' => '12');

	   // Loop dos Anos

	   for($ano=2009; $ano<=date('Y'); $ano++) { ?>

       <div class="ano">
            <div class="titulo">FOLHAS DE PAGAMENTO <span class="destaque"><?=$ano?></span></div>

            <table cellpadding="4" cellspacing="0" class="relacao">
              <tr class="secao">
                <td width="25%">Mês</td>
                <td width="15%" align="center">Total de Participantes</td>
                <td width="12%" align="center">GERAR</td>
              </tr>

	 <?php // Loop dos Meses

	       foreach($meses as $nome_mes => $mes) { ?>		   

            <tr class="linha_<?php if($cor++%2==0) { ?>um<? } else { ?>dois<? } ?>">
               <td><?=$nome_mes?></td>
               <td align="center"><?=$total_geral_participantes?></td>
               <td align="center"><a href="ir2.php?mes=<?=$mes?>&ano=<?=$ano?>&tipo=2" target="_blank" title="Gerar IRRF | mês: <?=$nome_mes?> | ano: <?=$ano?>"><img src="imagens/pdf.jpg" width="25" height="25" alt="pdf"></a></td>
            </tr>
        
		  <?php unset($total_geral_participantes); 
		        } // Fim do Loop dos Meses ?>
          </table>
		 </div>  
 	   <?php } // Fim do Loop dos Anos ?>
  </td>
 </tr>
</table>
</body>
</html>





<table align="center" cellpadding="0" cellspacing="0" class="corpo" id="topo">
 <?php $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$_GET[regiao]' AND id_master = '$Master'");
       $regiao = mysql_fetch_assoc($qr_regiao);
       $qr_folha = mysql_query("SELECT * FROM rh_folha WHERE status = '3' AND regiao = '$regiao[id_regiao]' ORDER BY mes ASC");
	   $numero_folha = mysql_num_rows($qr_folha);
	   if(!empty($numero_folha)) { ?>
 <tr>
   <td align="center" bgcolor="#F1F1F1">
      <table cellpadding="0" cellspacing="0" border="0" width="95%">
         <tr>    
          <td align="center">
        <a class="especial" onClick="document.getElementById('tabela1').style.display='';document.getElementById('oculta').style.display=''; document.getElementById('ver').style.display='none';" id="ver">IMPOSTO DE RENDA RETIDO NA FONTE  <span class="destaque">CLT</span></a>
        <a class="especial_ativo" style="display:none;" onClick="document.getElementById('tabela1').style.display='none'; document.getElementById('oculta').style.display='none'; document.getElementById('ver').style.display='';" id="oculta">FOLHAS DE PAGAMENTO <span class="destaque">CLT</span></a>
    	  </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr id="tabela1" style="display:none;">
   <td>
     <table align="center" cellpadding="4" cellspacing="0" class="relacao" style="margin-top:10px;">
      <tr class="secao">
        <td colspan="5">&lt;PROJETO&gt;</td>
        </tr>
      <tr class="secao">
        <td width="27%">Nome</td>
        <td width="11%">CPF</td>
        <td width="13%">Base IRRF</td>
        <td width="13%">Valor Retido</td>
        <td width="36%" align="center">PREFER&Ecirc;NCIA</td>
        </tr>
    
    <tr class="linha_<? if($alternateColor++%2==0) { ?>um<? } else { ?>dois<? } ?>">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>R$ </td>
    <td align="center">&nbsp;</td>
    </tr>
    <tr class="linha_<? if($alternateColor++%2==0) { ?>um<? } else { ?>dois<? } ?>">
      <td colspan="4" align="right" bgcolor="#CCCCCC">Total de IRRF do Proejeto &lt;PROJETO&gt; em &lt;ANO&gt;</td>
      <td align="center" bgcolor="#DDDDDD">R$ </td>
    </tr>
    <?php } ?>
