<?php 
include '../../conn.php';
include('../include/restricoes.php');
include "../../funcoes.php";
include "../include/criptografia.php";
//include "../../classes_permissoes/botoes.class.php";

//$permissao = new Botoes();
//$permissao->verifica_permissao(8);



if($_POST['pronto'] == "cadastro" && $_POST['passo'] == "1") {
	
	if(isset($_POST["atividade"])) {
	    $atividades = NULL;
	    foreach($_POST["atividade"] as $post_atividade) {
             $atividades .= "$post_atividade/";
        }
	}

	header("Location: edicao.php?passo=2&m=$_POST[link_master]&curso=$_POST[curso]&atividades=$atividades");
	
} elseif($_POST['pronto'] == "cadastro" && $_POST['passo'] == "2") {
	
	mysql_query("UPDATE aulas SET atividades = '$_POST[atividades]', autor_atualizacao = '$_COOKIE[logado]', data_atualizacao = NOW() WHERE aula_id = '$_POST[curso]'") or die(mysql_error());
	header("Location: cursos.php?sucesso=edicao&m=$_POST[link_master]&curso=$_POST[curso]");
	
}
?>
<html>
<head>
<title>Administração de Cursos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<script src="../js/funcoes.js" type="text/javascript"></script>
<style type="text/css">
div.opcoes {
        display: none;
}
</style>
</head>
<body>
   <div id="corpo">
        <div id="menu" class="projeto">
            <?php include "include/menu.php"; ?>
        </div>
        <div id="conteudo">
           <?php 
		   // Passo 1 //
		   if((empty($_GET['passo'])) or ($_GET['passo'] == "1")) { ?>
           
           <form name="curso" method="post" action="<?=$_SERVER['PHP_SELF']?>">
                    
            <?php 
			// Consulta da Aula
			$qr_curso = mysql_query("SELECT * FROM aulas WHERE aula_id = '$_GET[curso]' AND aula_master = '$Master' AND status = 'on'");		
			$curso = mysql_fetch_assoc($qr_curso);
			
			// Listando as Aulas
			$qr_select_cursos = mysql_query("SELECT * FROM aulas WHERE aula_master = '$Master' AND status = 'on'");
			$numero_select_cursos = mysql_num_rows($qr_select_cursos);
			 if(!empty($numero_select_cursos)) { ?>
             
             <h1><span>Edi&ccedil;&atilde;o de Curso</span></h1>
             Selecione o curso: <select name="curso" onChange="location.href = this.value;">
			 <?php while($select_curso = mysql_fetch_assoc($qr_select_cursos)) { ?>
             <option<?php if($_GET['curso'] == $select_curso['aula_id']) { echo ' selected="selected"'; } ?> value="edicao.php?m=<?=$_GET['m']?>&curso=<?=$select_curso['aula_id'];?>"><?=$select_curso['nome'];?></option>
            <?php } ?>
            </select> 


            <?php // Separando as Regiões
		    $qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = '$Master' AND status = '1'");
            $numero_regioes = mysql_num_rows($qr_regioes);
		    if(!empty($numero_regioes)) {
			  // Verificando se contém Atividades Ativas na Região
			$qr_pre_cursos = mysql_query("SELECT * FROM curso WHERE status = '1'");
			$pre_curso = mysql_num_rows($qr_pre_cursos);
			if(!empty($pre_curso)) { ?>
            
            
            
          <h1>Selecione as Atividades</h1>
          <table cellspacing="0" cellpadding="4" class="relacao">
            <tr class="secao">
              <td><input id="seleciona" type="checkbox" onClick="selecionar_tudo(); exibe()"> 
                <input id="deseleciona" type="checkbox" onClick="deselecionar_tudo(); exibe()" style="display:none;"></td>
              <td width="50%">Atividade</td>
              <td width="20%">Região</td>
              <td width="20%">Tipo</td>
              <td width="10%">Participantes</td>
            </tr>
            
            
            
            <?php // Listando as Atividades
			      } $qr_atividades = mysql_query("SELECT * FROM curso WHERE status = '1'");
			      while($atividade = mysql_fetch_assoc($qr_atividades)) {
				  // Total de Participantes por Atividade
				  if($atividade['tipo'] == "1") {
				  $qr_participantes = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '$atividade[tipo]' AND status = '1' AND id_curso = '$atividade[id_curso]'");
				  } elseif($atividade['tipo'] == "2") {
				  $qr_participantes = mysql_query("SELECT * FROM rh_clt WHERE tipo_contratacao = '$atividade[tipo]' AND status = '10' AND id_curso = '$atividade[id_curso]'");
				  } elseif($atividade['tipo'] == "3") {
				  $qr_participantes = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '$atividade[tipo]' AND status = '1' AND id_curso = '$atividade[id_curso]'");
				  }
				  $participantes = mysql_num_rows($qr_participantes);
				  $final_participantes = $final_participantes + $participantes;
				  if(!empty($participantes)) {
					  $checkbox_atividades = explode("/", $curso['atividades']);
				   ?>                  
                   
            <tr onClick="mudar_cor(this);mostraDiv('opcoes');" onMouseOver="this.style.cursor='pointer';" class="linha_<? if($alternateColor++%2==0) { ?>um<? } else { ?>dois<? } ?>" <?php if(in_array($atividade['id_curso'], $checkbox_atividades)) { echo 'style="background-color:#dee3ed;"'; } ?>>
              <td><input onClick="mudar_cor_chk(this,0);mostraDiv('opcoes');" name="atividade[]" type="checkbox" value="<?=$atividade['id_curso']?>" <?php if(in_array($atividade['id_curso'], $checkbox_atividades)) { echo "checked"; } ?>></td>
              <td><?=$atividade['nome']?></td>
              <td><?php $qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$atividade[id_regiao]' AND id_master = '$Master' AND status = '1'");
                        $regiao = mysql_fetch_assoc($qr_regioes); echo $regiao['regiao']; ?></td>
              <td><? if($atividade['tipo'] == 1) { echo "Autônomo"; } elseif($atividade['tipo'] == 2) { echo "Clt"; } elseif($atividade['tipo'] == 3) { echo "Cooperado"; } ?></td>
              <td><?=$participantes?></td>
            </tr>
            
            
            <?php } } ?>
            </table>
            <input name="link_master" value="<?=$_GET['m']?>" type="hidden" />
            <input name="pronto" value="cadastro" type="hidden" />
            <input name="passo" value="1" type="hidden" />
            <input value="Adicionar Atividades" type="submit" class="botao" />
            </form>
            <?php } } else { echo "<p style='margin-bottom:40px;'></p><h1>Nenhum Relatório Disponível</h1>"; }
			      // Fim do Passo 1 //
				  	  
				  // Passo 2 //
		          } elseif ($_GET['passo'] == "2") { ?>             
                  <div id="relatorio">
    <?php $qr_curso = mysql_query("SELECT * FROM aulas WHERE aula_id = '$_GET[curso]' AND status = 'on'");
		  $curso = mysql_fetch_assoc($qr_curso);
		  $get_atividades = explode("/", $_GET['atividades']); 
		  // Total de Participantes por Atividade
		for($a=0; $a<(count($get_atividades) - 1); $a++) {
			
		$qr_pre_participantes = mysql_query("SELECT * FROM curso WHERE id_curso = '$get_atividades[$a]' AND status = '1'");
		$pre_participantes = mysql_fetch_assoc($qr_pre_participantes);
		
				if($pre_participantes['tipo'] == "1") {
				$qr_participantes = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '$pre_participantes[tipo]' AND status = '1' AND id_curso = '$pre_participantes[id_curso]'"); 
				} elseif($pre_participantes['tipo'] == "2") {
				$qr_participantes = mysql_query("SELECT * FROM rh_clt WHERE tipo_contratacao = '$pre_participantes[tipo]' AND status = '10' AND id_curso = '$pre_participantes[id_curso]'"); 
				} elseif($pre_participantes['tipo'] == "3") {
				$qr_participantes = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '$pre_participantes[tipo]' AND status = '1' AND id_curso = '$pre_participantes[id_curso]'"); 
				}
				
		$participantes = mysql_num_rows($qr_participantes);
		$total_participantes = $total_participantes + $participantes; 
		
		} 
		//
		  for($b=0; $b<=count($get_atividades); $b++) {
		  $qr_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$get_atividades[$b]' AND status = '1'");
		  $atividade = mysql_fetch_assoc($qr_atividade);
		  $nome_atividades[] = $atividade['nome'];
		  }
		  ?>
	      <h1 style="margin:0px;">Relatório das Atividades Adicionadas</h1>
          <p>&nbsp;</p>
          <b>Curso:</b> <?=$curso['nome']?> 
          <p>&nbsp;</p>
          <b>Atividades:</b>
          <?php for($c=0; $c<count($nome_atividades); $c++) { echo "<br>$nome_atividades[$c]"; }
		  echo "<b>Participantes:</b> $total_participantes"; ?>
          <p>&nbsp;</p>
            <form method="post" action="<?=$_SERVER['PHP_SELF']?>">
            <input name="curso" value="<?=$_GET['curso']?>" type="hidden" />
            <input name="atividades" value="<?=$_GET['atividades']?>" type="hidden" />
            <input name="link_master" value="<?=$_GET['m']?>" type="hidden" />
            <input name="pronto" value="cadastro" type="hidden" />
            <input name="passo" value="2" type="hidden" />
            <input value="Concluir" type="submit" class="botao" />
            <input value="Cancelar" type="button" onClick="javascript:location.href = 'edicao.php?m=<?=$_GET['m']?>'" class="botao" />
            </form>      
            
		          </div>
                  
    <?php } // Fim do Passo 2 // ?>
    
        </div>
        <div id="rodape">
            <?php include "include/rodape.php"; ?>
        </div>
   </div>
</body>
</html>