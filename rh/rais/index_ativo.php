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
<title>Gerar RAIS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body>
<table align="center" cellpadding="0" cellspacing="0" class="corpo" id="topo">
  <tr>
	<td align="center">
	  <img src="imagens/logo_rais.jpg" width="357" height="150">
    </td>
  </tr>
    <tr>
       <td align="center"><h1>ANO BASE <span class="destaque"><?php echo date('Y') - 1; ?></span></h1></td>
    </tr>
    <tr>
       <td align="center">
       <?php $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$_GET[regiao]' AND id_master = '$Master'");
             $regiao = mysql_fetch_assoc($qr_regiao);
		     $numero_regiao = mysql_num_rows($qr_regiao);
	         if(!empty($numero_regiao)) { ?>
      <table border="0" cellpadding="5" cellspacing="0" style="border:1px solid #ddd; width:95%;">
        <tr>
          <td width="196" align="center" valign="baseline">
          <a href="rais.php?regiao=<?=$_GET['regiao']?>" target="_blank">
          <img src="icones/pdf.jpg" width="46" height="50" border="0"></a>
          <span class="campotexto"><br>
          Gerar RAIS em arquivo PDF</span></td>
          <td width="188" align="center" valign="baseline">
          <a href="raistexto.php?regiao=<?=$_GET['regiao']?>" target="_blank">
          <img src="icones/text.jpg" width="50" height="50" border="0"></a>
          <span class="campotexto"><br>
          Gerar RAIS em arquivo TXT</span></td>
        </tr>
      </table>
</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <?php $qr_rais = mysql_query("SELECT * FROM rais WHERE regiao = '$regiao[id_regiao]' ORDER BY id ASC");
        $numero_rais = mysql_num_rows($qr_rais);
		if(!empty($numero_rais)) { ?>
<tr class="historico">
  <td align="center" colspan="2">Hist&oacute;rico de Documentos Gerados</td>
</tr>
<tr>
  <td align="center"><br>
  <table cellpadding="4" cellspacing="0" style="border:1px solid #ddd; width:95%; font-weight:bold;">
  <tr class="secao">
    <td width="30%">Data</td>
    <td width="15%">Ano Base</td>
    <td width="15%">Tipo</td>
    <td width="40%">Autor</td>
    </tr>
    <?php while($rais = mysql_fetch_assoc($qr_rais)) { ?>
    <tr class="linha_<?php if($alternateColor++%2==0) { echo "um"; } else { echo "dois"; } ?>" style="font-size:11px; font-weight:bold; font-family:'Courier New', Courier, monospace; padding:4px; font-weight:normal;">
    <td><?php echo implode("/", array_reverse(explode("-", substr($rais['data'],0,10)))); echo " às "; echo substr($rais['data'],11,5); ?></td>
    <td style="color:#F63;"><?=$rais['ano_base']?></td>
    <td><? if($rais['tipo'] == 'pdf') { ?><img src="icones/pdf.jpg" width="25" height="25" alt="pdf">
        <? } elseif($rais['tipo'] == 'txt') { ?> <img src="icones/text.jpg" width="23" height="23" alt="txt">
        <? } ?>
        </td>
    <td><? $qr_autor = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$rais[autor]'");
	       $autor = mysql_fetch_assoc($qr_autor); 
		   echo $autor['nome']; ?>
    </td>
    </tr>
    <?php } ?>
    </table>
  </td>
</tr>
<?php } else { ?>
<tr><td align="center"><p>&nbsp;</p><i>Nenhum documento gerado até o momento.</i></td></tr>
<?php } } else { ?>
          <tr><td align="center"><p>&nbsp;</p><i>Região não encontrada</i></td></tr>
      <?php } ?>
<tr>
  <td>&nbsp;</td>
</tr>
</table>
</body>
</html>