<?php 
include "include/restricoes.php";
include "../../funcoes.php";
include "../include/criptografia.php";
?>
<html>
<head>
<title>Administração de Cursos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<link href="../css/spry.css" rel="stylesheet" type="text/css">
<script src="../js/spry.js" type="text/javascript"></script>
</head>
<body>
   <div id="corpo">
        <div id="menu" class="curso">
            <?php include "include/menu.php"; ?>
        </div>
        <div id="conteudo">
        <?php if($_GET['sucesso'] == "curso") { ?><div align="center" style="font-weight:bold;">O curso foi cadastrado com sucesso!</div><?php } if($_GET['sucesso'] == "edicao") { ?><div align="center" style="font-weight:bold;">As atividades foram adicionadas ao curso com sucesso!</div>
		<?php } 
		    // Listando os Cursos
			$qr_cursos = mysql_query("SELECT * FROM aulas WHERE aula_master = '$Master' AND status = 'on'");
			$numero_cursos = mysql_num_rows($qr_cursos);
	        if(!empty($numero_cursos)) { ?>
              <h1><span>Lista de Cursos</span></h1>
          <table cellspacing="0" cellpadding="4" class="relacao">
            <tr class="secao">
              <td width="25%">Nome</td>
              <td width="20%">&Aacute;rea</td>
              <td width="5%">Aulas</td>
              <td width="32%">Dias da Semana</td>
              <td width="3%">Atividades</td>
              <td width="15%">Participantes</td>
            </tr>
            </table>
            <div id="Accordion1" class="Accordion" tabindex="0">
            
		<?php // Início do Loop
			  while($curso = mysql_fetch_assoc($qr_cursos)) { ?>
             <div class="AccordionPanel">
          <div class="AccordionPanelTab<? if($alternateColor++%2==0) { ?>_um<? } else { ?>_dois<? } ?>">
           <table cellspacing="0" cellpadding="4" class="relacao" style="width:100%;">
            <tr onClick="mudar_cor(this);mostraDiv('opcoes');" class="linha_<?php if($alternateColor2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>_accordion" <?php if($curso['aula_id'] == $_GET['curso']) { ?>style="background-color:#dee3ed;"<?php } ?>>
              <td width="25%"><?=$curso['nome']?></td>
              <td width="20%"><?=$curso['area']?></td>
              <td width="5%"><?=$curso['qnt_aulas']?></td>
              <td width="32%"><?=substr($curso['dias_semana'], 0, -2)?></td>
              <td width="3%"><?php 
			  $preb_atividades = explode('/', $curso['atividades']);
		      echo count($preb_atividades);
			  ?></td>
              <td width="15%">
			  <?php // Total de Participantes por Atividade
	    $pre_atividades = explode('/', $curso['atividades']);
		for($a=0; $a<(count($pre_atividades) - 1); $a++) {
			
				$qr_pre_participantes = mysql_query("SELECT * FROM curso WHERE id_curso = '$pre_atividades[$a]' AND status = '1'");
				$pre_participantes    = mysql_fetch_assoc($qr_pre_participantes);
		
				if($pre_participantes['tipo'] == '1') {
					$qr_participantes = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '$pre_participantes[tipo]' AND status = '1' AND id_curso = '$pre_participantes[id_curso]'"); 
				} elseif($pre_participantes['tipo'] == '2') {
					$qr_participantes = mysql_query("SELECT * FROM rh_clt WHERE tipo_contratacao = '$pre_participantes[tipo]' AND status = '10' AND id_curso = '$pre_participantes[id_curso]'"); 
				} elseif($pre_participantes['tipo'] == '3') {
					$qr_participantes = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '$pre_participantes[tipo]' AND status = '1' AND id_curso = '$pre_participantes[id_curso]'"); 
				}
				
				$participantes = mysql_num_rows($qr_participantes);
				$total_participantes = $total_participantes + $participantes;
		
		} echo $total_participantes; unset($participantes); unset($total_participantes);
		 ?>
              </td>
            </tr>
            </table>
          </div>
          <div class="AccordionPanelContent">
          <?php 
		  $get_atividades = explode("/", $curso['atividades']);
		  for($b=0; $b<=sizeof($get_atividades); $b++) {
		  $qr_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$get_atividades[$b]' AND status = '1'");
		  $atividade = mysql_fetch_assoc($qr_atividade);
		  $nome_atividades[] = $atividade['nome'];
		  }
		  ?>
	      <b style="padding-left:10px;"><?php if(!empty($curso['atividades'])) { echo "Atividades Adicionadas:"; } else { echo "Nenhuma Atividade Adicionada"; } ?></b>
          <a class="fixo" href="edicao.php?m=<?=$_GET['m']?>&curso=<?=$curso['aula_id']?>"><?php if(!empty($curso['atividades'])) { echo "Editar / "; } ?>Adicionar Atividades</a>
          <?php for($c=0; $c<sizeof($nome_atividades); $c++) { ?>
          <p class="linha_<? if($alternateColor2++%2==0) { ?>um<? } else { ?>dois<? } ?>" style="padding-left:10px;">
          <?=$nome_atividades[$c]?> 
          <?php } unset($get_atividades);
				  unset($nome_atividades); ?>
          </div>
              </div>
            <?php } ?>
            </div>
            <?php } else { echo "<p style='margin-bottom:40px;'></p><h1>Nenhum Relatório Disponível</h1>"; } ?>       
        </div>
        <div id="rodape">
            <?php include "include/rodape.php"; ?>
        </div>
   </div>
<script type="text/javascript">
var Accordion1 = new Spry.Widget.Accordion("Accordion1", { enableAnimation: false, useFixedPanelHeights: false, defaultPanel: -1 });
</script>
</body>
</html>