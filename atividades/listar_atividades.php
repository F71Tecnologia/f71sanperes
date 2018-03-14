<?php
if(empty($_COOKIE['logado'])){
	print "Efetue o Login<br><a href='login.php'>Logar</a> ";
	exit;
}

include "../conn.php";
include "../classes/regiao.php";
include "../classes/funcionario.php";


$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$master = $row_user['id_master'];

$id		= $_REQUEST['id'];
$regiao		= $_GET['regiao'];
$nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'"),0);



$USER = new funcionario();

?>
<html><head><title>:: Intranet ::</title>

<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<meta http-equiv='Cache-Control' content='No-Cache'>
<meta http-equiv='Pragma'        content='No-Cache'>
<meta http-equiv='Expires'       content='No-Cache'>

<meta http-equiv='Expires' content='Fri, Jan 01 1900 00:00:00 GMT'/>   
<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate'/>   
<meta http-equiv='Cache-Control' content='post-check=0, pre-check=0'/>   
<meta http-equiv='Pragma' content='no-cache'/>

<link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/abas_anos.js"></script>
<!--script type="text/javascript" src="../js/highslide-with-html.js"></script-->

<link rel="stylesheet" type="text/css" href="../js/highslide.css" />
<link rel="stylesheet" href="../js/lightbox.css" type="text/css" media="screen" />
<script type="text/javascript">
    hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = '../rounded-white';


</script>


</head>
<body>

<div id="corpo">
	<div id="conteudo">
       <!--<span style="float:left;"><br><a href='../index.php?regiao=<?php echo $id_regiao;?>' class='link'><img src='../imagens/voltar.gif' border=0></a>
        </span>-->
        <span style="clear:left;"></span>
        
        		 <div class="right"><?php include('../reportar_erro.php'); ?></div>
       			 <div class="clear"></div>
                 
        		<img src="../imagens/logomaster<?php echo $master?>.gif"/>
                	<h3><img src="../img_menu_principal/atividade.png" width="21" height="20"/>  VISUALIZAR FUNÇÕES<BR>  (<?php echo $nome_regiao;?>)</h3>
                
                 <?php
                 $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' ");
				if($row_func['tipo_usuario'] == 6) {
		
							$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1' AND id_projeto = '3295' ORDER BY nome");
							
						} else {
							
							$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1' ORDER BY nome");
						}

				
				
				 while($row_projeto = mysql_fetch_assoc($qr_projeto)):
				 
				 ///NOME PROJETO
				 if($row_projeto['id_projeto'] != $projeto_anterior){ 
							echo '<a href="#" class="titulo_ano">'.$row_projeto['nome'].'</a>';
							echo '<div  class="folhas" style="display:none;">';
							
							}
							
					
					$qr_tipo_contrato = mysql_query("SELECT * FROM tipo_contratacao WHERE 1");
					while($row_tipo_contrato = mysql_fetch_assoc($qr_tipo_contrato)):
					
                                                $sql_curso = "SELECT A.*,B.cod FROM curso AS A
                                                                                LEFT JOIN rh_cbo AS B ON (A.cbo_codigo=B.id_cbo)
                                                                                WHERE A.tipo = '$row_tipo_contrato[tipo_contratacao_id]' AND A.status = 1  AND A.id_regiao = '$regiao' 
                                                                                AND A.campo3 = '$row_projeto[id_projeto]' ";
						//echo $sql_curso.'<br>';
                                                $qr_curso = mysql_query($sql_curso);
						
						if(mysql_num_rows($qr_curso)!= 0){
							
							if($row_tipo_contrato['tipo_contratacao_id'] != $tipo_contratacao_anterior){ 
							
							echo '<h3 class="titulo_projeto">'.$row_tipo_contrato['tipo_contratacao_nome'].'</h3>';
													
							echo '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="relacao">';
							echo '<tr class="titulo_tabela1">
									<td>CÓD.</td>
									<td align="left">FUNÇÃO</td>
									<td>CBO</td>
									<td>VALOR</td>
									<td>QUANTIDADE MÁXIMA</td>								
									<td>EDITAR</td>
								 </tr>';
							
							}
						
						}
						
						
						
						while($row_curso = mysql_fetch_assoc($qr_curso)):
						
						$nomeT = str_replace("CAPACITANDO EM ","CAP. EM ",$row_curso['campo2']);
						$class = (($i++ % 2) == 0)? 'class="linha_um"' :  'class="linha_dois"' ;
						$link = "";
						?>	
						<tr <?php echo $class;?>>
                                                    <td><?php echo $row_curso['id_curso'];?></td>
                                                    <td align="left"><?=$nomeT?></a></td>
                                                    <td align="left"><?=$row_curso['cod']?></a></td>
                                                    <td><?="R$ ".$row_curso['salario'];  ?></td>
                                                    <td><?php echo ($row_curso['qnt_maxima'] == 0)? 'Não informado.': $row_curso['qnt_maxima']; ?></td>                                             
                                                    <td><a href='../ver_tudo.php?id=2&ativi=<?php echo $row_curso['id_curso']?>&regiao=<?php echo $regiao; ?>' target='_blanck' style='text-decoration:none;' onClick=" return hs.htmlExpand(this,{ objectType: 'iframe'})"><img src="../imagens/editar_projeto.png"  title="EDITAR"/></a></td>
                                                </tr>
                                                <?php
						endwhile;
						
						echo '<tr><td>&nbsp;</td></tr>
						</table>';
						$tipo_contratacao_anterior = $row_tipo_contrato['tipo_contratacao_id'];
						
					endwhile; //tipo_anterior
					
					$projeto_anterior = $row_projeto['id_projeto'];
					echo '</div>';
                endwhile; //projeto
				
				?>
    			
<p>&nbsp;</p>
<p>&nbsp;</p>
                
    </div>

</div>


</body>
</html>