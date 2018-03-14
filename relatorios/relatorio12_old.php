<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
} else {

include "../conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$projeto = $_REQUEST['pro'];
$regiao = $_REQUEST['reg'];

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($result_projeto);

$data_hoje = date('d/m/Y');

$result_autonomo = mysql_query("SELECT * FROM autonomo WHERE status = '1'  AND id_projeto = '$projeto' AND tipo_contratacao = '1' ORDER BY nome");
$num_autonomo = mysql_num_rows($result_autonomo);

$result_clt = mysql_query("SELECT * FROM rh_clt WHERE status = '10' AND id_projeto = '$projeto' ORDER BY nome");
$num_clt = mysql_num_rows($result_clt);

$result_cooperado = mysql_query("SELECT * FROM autonomo WHERE status = '1' AND id_projeto = '$projeto' AND tipo_contratacao = '3' ORDER BY nome");
$num_cooperado = mysql_num_rows($result_cooperado);

$result_pj = mysql_query("SELECT * FROM autonomo WHERE status = '1' AND id_projeto = '$projeto' AND tipo_contratacao = '4' ORDER BY nome");
$num_pj = mysql_num_rows($result_pj);
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relat&oacute;rio de Documentos</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>RELAT&Oacute;RIO DE DOCUMENTOS</strong><br>
         <?=$row_master['razao']?>
         <table width="500" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
            <tr style="color:#FFF;">
              <td width="150" height="22" class="top">PROJETO</td>
              <td width="150" class="top">REGIÃO</td>
              <td width="200" class="top">TOTAL DE PARTICIPANTES</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?=$row_projeto['nome']?></b></td>
              <td align="center"><b><?=$row_projeto['regiao']?></b></td>
              <td align="center"><b><?php echo $num_clt+$num_cooperado+$num_autonomo+$num_pj; ?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3">
    
    <?php if(!empty($num_autonomo)) { ?>
   
      <div class="descricao">Relatório de Documentos de Autonômos</div>
      <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
         <tr class="secao">
         <td width="35%" valign="bottom" align="right">Nome</td>
         <td width="65%" height="150" bgcolor="#FFFFFF" valign="bottom">
             
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
               <tr>
                 <td class="documento"><img src="../imagens/doc_contrato.gif" width="10" height="53"></td>
                 <td class="documento"><img src="../imagens/doc_distrato.gif" width="10" height="48"></td>
                 <td class="documento"><img src="../imagens/doc_tvsorrindo.gif" width="10" height="72"></td>
                 <td class="documento"><img src="../imagens/doc_declaracao_renda.gif" width="23" height="67"></td>
                 <td class="documento"><img src="../imagens/doc_certificado.gif" width="10" height="64"></td>
                 <td class="documento"><img src="../imagens/doc_2via_contrato.gif" width="22" height="73"></td>
                 <td class="documento"><img src="../imagens/doc_encaminhamento_bancario.gif" width="22" height="109"></td>
               </tr>
            </table>
             
        </td>
      </tr>

      <?php while($row_autonomo = mysql_fetch_array($result_autonomo)) { ?>

      <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
        <td align="right"><?=$row_autonomo['nome']?></td>
        <td>
        
           <table cellpadding="0" cellspacing="0" border="0" width="100%">
               <tr>
                 <?php $qr_docs_autonomo = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '1' ORDER BY id_doc ASC");
				       while($docs_autonomo = mysql_fetch_assoc($qr_docs_autonomo)) {

					   $qr_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '$docs_autonomo[id_doc]' AND id_clt = '$row_autonomo[id_autonomo]'");  $verifica = mysql_num_rows($qr_verifica);
					   if(!empty($verifica)) {
	                     $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
	                   } else {
	                     $img = '<img src="../imagens/naoassinado.gif" width="15" height="17">';
	                   }
					   ?> 
                 <td class="documento"><?=$img?></td>
                 <?php } ?>
               </tr>
            </table>
        
        </td>
      </tr>
      
     <?php } ?>
     
     <tr class="secao">
        <td colspan="10" align="center">TOTAL DE AUTÔNOMOS: <?php echo $num_autonomo; ?></td>
      </tr>
  </table>
  
     <?php } ?>

     </td>
  </tr>
   <tr>
     <td colspan="3">
    
    <?php if(!empty($num_clt)) { ?>
  
    <div class="descricao">Relatório de Documentos de CLTs</div>
    <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
      <tr class="secao">
         <td width="35%" valign="bottom" align="right">Nome</td>
         <td width="65%" height="150" bgcolor="#FFFFFF" valign="bottom">
             
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
               <tr>
                 <td class="documento"><img src="../imagens/doc_advertencia.gif" width="11" height="72"></td>
                 <td class="documento"><img src="../imagens/doc_aviso_previo.gif" width="11" height="74"></td>
                 <td class="documento"><img src="../imagens/doc_carta_referencia.gif" width="22" height="63"></td>
                 <td class="documento"><img src="../imagens/doc_contrato_trabalho.gif" width="22" height="71"></td>
                 <td class="documento"><img src="../imagens/doc_demissao.gif" width="11" height="58"></td>
                 <td class="documento"><img src="../imagens/doc_dispensa.gif" width="12" height="54"></td>
                 <td class="documento"><img src="../imagens/doc_dispensa_valetransporte.gif" width="24" height="96"></td>
                 <td class="documento"><img src="../imagens/doc_exame_admissional.gif" width="22" height="72"></td>
                 <td class="documento"><img src="../imagens/doc_exame_demissional.gif" width="22" height="72"></td>
                 <td class="documento"><img src="../imagens/doc_ficha_cadastroclt.gif" width="22" height="80"></td>
                 <td class="documento"><img src="../imagens/doc_ficha_cadastrosalariofamilia.gif" width="22" height="109"></td>
                 <td class="documento"><img src="../imagens/doc_inscricaopis.gif" width="13" height="99"></td>
                 <td class="documento"><img src="../imagens/doc_solicitacao_salariofamilia.gif" width="23" height="87"></td>
                 <td class="documento"><img src="../imagens/doc_solicitacao_valetransporte.gif" width="25" height="96"></td>
                 <td class="documento"><img src="../imagens/doc_suspencao.gif" width="13" height="65"></td>
                 <td class="documento"><img src="../imagens/doc_tvsorrindo.gif" width="10" height="72"></td>
               </tr>
            </table>
             
        </td>
      </tr>
      
      <?php while($row_clt = mysql_fetch_assoc($result_clt)) { ?>
       
       <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
        <td align="right"><?=$row_clt['nome']?></td>
        <td>
        
           <table cellpadding="0" cellspacing="0" border="0" width="100%">
               <tr>
                 <?php $qr_docs_clt = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '2' ORDER BY documento ASC");
				       while($docs_clt = mysql_fetch_assoc($qr_docs_clt)) {
					   $qr_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '$docs_clt[id_doc]' AND id_clt = '$row_clt[id_clt]'");  $verifica = mysql_num_rows($qr_verifica);
					   if((!empty($row_clt['pis'])) and $docs_clt['documento'] == "Inscrição no PIS") {
						 $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
					   } else {
						   if(!empty($verifica)) {
							 $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
						   } else {
							 $img = '<img src="../imagens/naoassinado.gif" width="15" height="17">';
						   }
					   }
					   ?> 
                 <td class="documento"><?=$img?></td>
                 <?php } ?>
               </tr>
            </table>
        
        </td>
       </tr>
      
     <?php } ?>
     
     <tr class="secao">
        <td colspan="10" align="center">TOTAL DE CLTS: <?php echo $num_clt; ?></td>
      </tr>
    </table>

    <?php } ?>

     </td>
   </tr>
   <tr>
  <td colspan="3">
  
  <?php if(!empty($num_cooperado)) { ?>
  
    <div class="descricao">Relatório de Documentos de Colaboradores</div>
    <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
      <tr class="secao">
         <td width="35%" valign="bottom" align="right">Nome</td>
         <td width="65%" height="150" bgcolor="#FFFFFF" valign="bottom">
             
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
               <tr>
                 <td class="documento"><img src="../imagens/doc_tvsorrindo.gif" width="10" height="72"></td>
                 <td class="documento"><img src="../imagens/doc_ficha_adesao.gif" width="22" height="62"></td>
                 <td class="documento"><img src="../imagens/doc_ficha_quotas.gif" width="24" height="62"></td>
                 <td class="documento"><img src="../imagens/doc_ficha_cadastro.gif" width="22" height="73"></td>
                 <td class="documento"><img src="../imagens/doc_desligamento.gif" width="12" height="83"></td>
                 <td class="documento"><img src="../imagens/doc_pis.gif" width="10" height="20"></td>
               </tr>
            </table>
             
        </td>
      </tr>
      
      <?php while($row_cooperado = mysql_fetch_array($result_cooperado)) { ?>
       
      <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
        <td align="right"><?=$row_cooperado['nome']?></td>
        <td>
        
           <table cellpadding="0" cellspacing="0" border="0" width="100%">
               <tr>
                 <?php $qr_docs_cooperado = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '3' ORDER BY id_doc ASC");
				       while($docs_cooperado = mysql_fetch_assoc($qr_docs_cooperado)) {
					   $qr_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '$docs_cooperado[id_doc]' AND id_clt = '$row_cooperado[id_autonomo]'");  $verifica = mysql_num_rows($qr_verifica);
					   if((!empty($row_cooperado['pis'])) and $docs_cooperado['documento'] == "PIS") {
						 $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
					   } else {
						   if(!empty($verifica)) {
							 $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
						   } else {
							 $img = '<img src="../imagens/naoassinado.gif" width="15" height="17">';
						   }
					   }
					   ?> 
                 <td class="documento"><?=$img?></td>
                 <?php } ?>
               </tr>
            </table>
        
        </td>
      </tr>
      
     <?php } ?>
     
     <tr class="secao">
        <td colspan="10" align="center">TOTAL DE COLABORADORES: <?php echo $num_cooperado; ?></td>
      </tr>
    </table>

    <?php } ?>
    
    </td>
   </tr>
   <tr>
  <td colspan="3">
  
  <?php if(!empty($num_pj)) { ?>
  
    <div class="descricao">Relatório de Documentos de Autônomos / PJ</div>
    <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
      <tr class="secao">
         <td width="35%" valign="bottom" align="right">Nome</td>
         <td width="65%" height="150" bgcolor="#FFFFFF" valign="bottom">
             
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
               <tr>
                 <td class="documento"><img src="../imagens/doc_tvsorrindo.gif" width="10" height="72"></td>   
               </tr>
            </table>
             
        </td>
      </tr>
      
      <?php while($row_pj = mysql_fetch_array($result_pj)) { ?>
       
      <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
        <td align="right"><?=$row_pj['nome']?></td>
        <td>
        
           <table cellpadding="0" cellspacing="0" border="0" width="100%">
               <tr>
                 <?php $qr_docs_cooperado = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '4' ORDER BY id_doc ASC");
				       while($docs_cooperado = mysql_fetch_assoc($qr_docs_cooperado)) {
					   $qr_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '$docs_cooperado[id_doc]' AND id_clt = '$row_pj[id_autonomo]'");  $verifica = mysql_num_rows($qr_verifica);
					   if((!empty($row_pj['pis'])) and $docs_cooperado['documento'] == "PIS") {
						 $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
					   } else {
						   if(!empty($verifica)) {
							 $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
						   } else {
							 $img = '<img src="../imagens/naoassinado.gif" width="15" height="17">';
						   }
					   }
					   ?> 
                 <td class="documento"><?=$img?></td>
                 <?php } ?>
               </tr>
            </table>
        
        </td>
      </tr>
      
     <?php } ?>
     
     <tr class="secao">
        <td colspan="10" align="center">TOTAL DE AUTÔNOMOS / PJ: <?php echo $num_pj; ?></td>
      </tr>
    </table>

    <?php } ?>
    </td>
  </tr>
  </table>
  </body>
</html>
<?php } ?>