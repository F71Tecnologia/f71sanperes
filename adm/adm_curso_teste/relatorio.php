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
</head>
<body>
   <div id="corpo">
        <div id="menu" class="curso">
            <?php include "include/menu.php"; ?>
        </div>
        <div id="conteudo">
        <?php 
	// Separando as Regiões
	$qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = '$Master' AND status = '1'");
    $numero_regioes = mysql_num_rows($qr_regioes);
		if(!empty($numero_regioes)) {
			  while($regiao = mysql_fetch_assoc($qr_regioes)) {
				  
			  // Verificando se contém Atividades Ativas na Região
			  $qr_pre_cursos = mysql_query("SELECT * FROM curso WHERE id_regiao = '$regiao[id_regiao]' AND status = '1'");
			  $pre_curso = mysql_num_rows($qr_pre_cursos);
			          if(!empty($pre_curso)) { ?>
             
        <h1><span>Região de <?=$regiao['regiao']?></span></h1>
          <table cellspacing="0" cellpadding="4" class="relacao">
            <tr class="secao">
              <td width="560&">Atividade</td>
              <td width="20%">Tipo</td>
              <td width="20%">Participantes</td>
            </tr>
            
     <?php }
	 // Listando as Atividades
	 $qr_cursos = mysql_query("SELECT * FROM curso WHERE id_regiao = '$regiao[id_regiao]' AND status = '1'");
			while($curso = mysql_fetch_assoc($qr_cursos)) {
				
			// Total de Participantes por Atividade
			if($curso['tipo'] == "1") {
				  $qr_participantes = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '$curso[tipo]' AND status = '1' AND id_curso = '$curso[id_curso]'");
			} elseif($curso['tipo'] == "2") {
				  $qr_participantes = mysql_query("SELECT * FROM rh_clt WHERE tipo_contratacao = '$curso[tipo]' AND status = '10' AND id_curso = '$curso[id_curso]'");
			} elseif($curso['tipo'] == "3") {
				  $qr_participantes = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '$curso[tipo]' AND status = '1' AND id_curso = '$curso[id_curso]'");
			}
		    $participantes = mysql_num_rows($qr_participantes);
            $total_participantes = $total_participantes + $participantes;
				     if(!empty($participantes)) { 
					 ?>
                     
            <tr class="linha_<? if($alternateColor++%2==0) { ?>um<? } else { ?>dois<? } ?>">
              <td><?=$curso['nome']?></td>
              <td><? if($curso['tipo'] == 1) { echo "Autônomo"; } elseif($curso['tipo'] == 2) { echo "Clt"; } elseif($curso['tipo'] == 3) { echo "Cooperado"; } ?></td>
              <td><?=$participantes?></td>
            </tr>
            
            <?php } } if(!empty($pre_curso)) { ?>
            
            <tr>
              <td></td>
              <td style="text-align:right; font-size:10px; vertical-align:bottom;">Total:</td>
              <td><?=$total_participantes?></td>
            </tr>
            
            <?php unset($pre_curso); } ?>
            
          </table>
          <p style="margin-bottom:40px;"></p>
          
          <?php 
		  // Total Final
		  $final_participantes = $final_participantes + $total_participantes;
          unset($total_participantes); unset($total_cooperados); 
		  }

				if(empty($final_participantes)) {
		              echo "<h1>Nenhum Relatório Disponível</h1>"; 
				} else {
				      echo "<h1>Total Final<br>Participantes: ".$final_participantes."</h1>";
				}
		  } 
		  ?>
        </div>
        <div id="rodape">
            <?php include "include/rodape.php"; ?>
        </div>
   </div>
</body>
</html>